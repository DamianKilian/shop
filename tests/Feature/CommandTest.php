<?php

namespace Tests\Feature;

use App\Mail\Logs;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\File;
use App\Models\Page;
use App\Models\Product;
use App\Models\Suggestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Attachment as MailablesAttachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class CommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_send(): void
    {
        Mail::fake();
        Storage::disk('logs')->put('laravel-error.log', 'logs contents');
        Config::set('my.log_to_emails', 'example@example.com');

        $this->artisan('logs:send')->assertExitCode(0);
        Mail::assertSent(Logs::class, function (Logs $mail) {
            return $mail->hasAttachment(
                MailablesAttachment::fromPath(storage_path('logs/laravel-error.log'))
            );
        });
    }

    public function test_logs_send_empty_emails(): void
    {
        Mail::fake();
        Storage::disk('logs')->put('laravel-error.log', 'logs contents');
        Config::set('my.log_to_emails', '');

        $this->artisan('logs:send')->assertExitCode(0);
        Mail::assertNotSent(Logs::class);
    }

    public function test_logs_send_empty_log_file(): void
    {
        Mail::fake();
        Storage::disk('logs')->put('laravel-error.log', '');
        Config::set('my.log_to_emails', 'example@example.com');

        $this->artisan('logs:send')->assertExitCode(0);
        Mail::assertNotSent(Logs::class);
    }

    public function test_logs_send_no_log_file(): void
    {
        Mail::fake();
        Storage::disk('logs')->delete('laravel-error.log');
        Config::set('my.log_to_emails', 'example@example.com');

        $this->artisan('logs:send')->assertExitCode(0);
        Mail::assertNotSent(Logs::class);
    }

    public function test_prune_temp(): void
    {
        $time = time();
        $weekAgo = $time - 604800;
        // $month = 2592000;
        $yearAgo = $time - 31536000;
        $tempStorage = Storage::fake('temp');
        $tempStorage->put('file1.jpg', 'content');
        touch($tempStorage->path('file1.jpg'), $weekAgo);
        $tempStorage->put('file2', 'content');
        touch($tempStorage->path('file2'), $weekAgo);
        $tempStorage->put('file3.jpg', 'content');
        touch($tempStorage->path('file3.jpg'), $yearAgo);
        $tempStorage->put('file4', 'content');
        touch($tempStorage->path('file4'), $yearAgo);

        $this->artisan('prune:temp')->assertExitCode(0);
        $this->assertTrue(['file1.jpg', 'file2'] == $tempStorage->files());
    }

    public function test_prune_suggestions(): void
    {
        // 7 suggestions
        Suggestion::factory()->count(3)->create();
        Suggestion::factory()->create([
            'last_used' => now()->subMonth(),
        ]);
        Suggestion::factory()->create([
            'last_used' => now()->subMonths(5),
        ]);
        Suggestion::factory()->create([
            'last_used' => now()->subMonths(13),
        ]);
        Suggestion::factory()->create([
            'last_used' => now()->subYears(2),
        ]);

        $this->artisan('prune:suggestions')->assertExitCode(0);
        $this->assertDatabaseCount('suggestions', 5);
    }

    public function test_prune_attachments(): void
    {
        $publicStorage = Storage::fake('public');
        $filesNum = 5;
        $i = 1;
        while ($i <= $filesNum) {
            $publicStorage->put("folder/img$i.jpg", 'content');
            $i++;
        }
        $removedFiles = [];
        // case1
        $f1 = Attachment::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img1.jpg', //remove
            'created_at' => now()->subMonths(2),
        ]);
        $removedFiles[] = 1;
        $f2 = Attachment::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img2.jpg', //remove
            'created_at' => now()->subMonths(2),
        ]);
        $removedFiles[] = 2;
        // case2
        $f3 = Attachment::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img3.jpg', //do not remove
            'created_at' => now()->subMonths(2),
        ]);
        $page = Page::factory()->create();
        $f4 = Attachment::factory()->create([
            'page_id' => $page->id,
            'product_id' => null,
            'url' => 'folder/img3.jpg',
        ]);
        // case3
        $f5 = Attachment::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img4.jpg', //do not remove
            'created_at' => now()->subMonths(2),
        ]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id
        ]);
        $f6 = Attachment::factory()->create([
            'page_id' => null,
            'product_id' => $product->id,
            'url' => 'folder/img4.jpg',
        ]);
        // case5
        $f7 = Attachment::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img5.jpg', //do not remove
            'created_at' => now()->subDay(),
        ]);

        $this->artisan('prune:attachments')->assertExitCode(0);
        $this->assertEquals(Attachment::all()->pluck('id')->toArray(), [$f4->id, $f6->id, $f7->id,]);
        $i = 1;
        while ($i <= $filesNum) {
            if (false === array_search($i, $removedFiles)) {
                $publicStorage->assertExists("folder/img$i.jpg");
            } else {
                $publicStorage->assertMissing("folder/img$i.jpg");
            }
            $i++;
        }
    }

    public function test_prune_files(): void
    {
        $publicStorage = Storage::fake('public');
        $filesNum = 7;
        $thumbnailsNum = 6;
        $i = 1;
        while ($i <= $filesNum) {
            $publicStorage->put("folder/img$i.jpg", 'content');
            $i++;
        }
        $t = 1;
        while ($t <= $thumbnailsNum) {
            $publicStorage->put("thumbnail-folder/thumbnail$t.jpg", 'content');
            $t++;
        }
        $removedFiles = [];
        $removedThumbnails = [];
        // case1
        $f1 = File::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img1.jpg', //remove
            'url_thumbnail' => 'thumbnail-folder/thumbnail1.jpg', //remove
            'created_at' => now()->subMonths(2),
        ]);
        $removedFiles[] = 1;
        $removedThumbnails[] = 1;
        $f2 = File::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img2.jpg', //remove
            'created_at' => now()->subMonths(2),
        ]);
        $removedFiles[] = 2;
        // case2
        $f3 = File::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img3.jpg', //do not remove
            'url_thumbnail' => 'thumbnail-folder/thumbnail2.jpg', //do not remove
            'created_at' => now()->subMonths(2),
        ]);
        $page = Page::factory()->create();
        $f4 = File::factory()->create([
            'page_id' => $page->id,
            'product_id' => null,
            'url' => 'folder/img3.jpg',
        ]);
        $page2 = Page::factory()->create();
        $f5 = File::factory()->create([
            'page_id' => $page2->id,
            'product_id' => null,
            'url' => 'folder/img3.jpg',
            'url_thumbnail' => 'thumbnail-folder/thumbnail2.jpg',
        ]);
        // case3
        $f6 = File::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img4.jpg', //remove
            'url_thumbnail' => 'thumbnail-folder/thumbnail3.jpg', //do not remove
            'created_at' => now()->subMonths(2),
        ]);
        $removedFiles[] = 4;
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id
        ]);
        $f7 = File::factory()->create([
            'page_id' => null,
            'product_id' => $product->id,
            'url' => 'folder/img5.jpg',
            'url_thumbnail' => 'thumbnail-folder/thumbnail3.jpg',
        ]);
        // case4
        $f8 = File::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img6.jpg', //do not remove
            'url_thumbnail' => 'thumbnail-folder/thumbnail4.jpg', //remove
            'created_at' => now()->subMonths(2),
        ]);
        $removedThumbnails[] = 4;
        $category2 = Category::factory()->create();
        $product2 = Product::factory()->create([
            'category_id' => $category2->id
        ]);
        $f9 = File::factory()->create([
            'page_id' => null,
            'product_id' => $product2->id,
            'url' => 'folder/img6.jpg',
            'url_thumbnail' => 'thumbnail-folder/thumbnail5.jpg',
        ]);
        // case5
        $f10 = File::factory()->create([
            'page_id' => null,
            'product_id' => null,
            'url' => 'folder/img7.jpg', //do not remove
            'url_thumbnail' => 'thumbnail-folder/thumbnail6.jpg', //do not remove
            'created_at' => now()->subDay(),
        ]);

        $this->artisan('prune:files')->assertExitCode(0);
        $this->assertEquals(File::all()->pluck('id')->toArray(), [$f4->id, $f5->id, $f7->id, $f9->id, $f10->id,]);
        $i = 1;
        while ($i <= $filesNum) {
            if (false === array_search($i, $removedFiles)) {
                $publicStorage->assertExists("folder/img$i.jpg");
            } else {
                $publicStorage->assertMissing("folder/img$i.jpg");
            }
            $i++;
        }
        $t = 1;
        while ($t <= $thumbnailsNum) {
            if (false === array_search($t, $removedThumbnails)) {
                $publicStorage->assertExists("thumbnail-folder/thumbnail$t.jpg");
            } else {
                $publicStorage->assertMissing("thumbnail-folder/thumbnail$t.jpg");
            }
            $t++;
        }
    }
}
