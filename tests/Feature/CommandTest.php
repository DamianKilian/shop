<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\File;
use App\Models\Page;
use App\Models\Product;
use App\Models\Suggestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommandTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_prune_files(): void
    {
        $this->prune_files(File::factory(), 'prune:files', 'files');
    }

    public function test_prune_attachments(): void
    {
        $this->prune_files(Attachment::factory(), 'prune:attachments', 'attachments');
    }

    public function prune_files($factory, $command, $table): void
    {
        $publicStorage = Storage::fake('public');
        // 5 pageFiles
        $publicStorage->put('test/img1.jpg', 'content');
        if ('files' === $table) {
            $publicStorage->put(env('THUMBNAILS_FOLDER') . '/img1.jpg', 'content');
        }
        $factory->create([
            'url' => 'test/img1.jpg',
        ]);
        $publicStorage->put('test/img2.jpg', 'content');
        $factory->create([
            'url' => 'test/img2.jpg',
            'created_at' => now()->subDay(),
        ]);
        $publicStorage->put('test/img3.jpg', 'content');
        $factory->create([
            'url' => 'test/img3.jpg',
            'created_at' => now()->subDays(5),
        ]);
        $publicStorage->put('test/img4.jpg', 'content');
        if ('files' === $table) {
            $publicStorage->put(env('THUMBNAILS_FOLDER') . '/img4.jpg', 'content');
        }
        $factory->create([
            'url' => 'test/img4.jpg',
            'created_at' => now()->subWeeks(5),
        ]);
        $publicStorage->put('test/img5.jpg', 'content');
        $factory->create([
            'url' => 'test/img5.jpg',
            'created_at' => now()->subMonths(5),
        ]);
        // files with id
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id
        ]);
        $page = Page::factory()->create();
        $publicStorage->put('test/img6.jpg', 'content');
        $factory->create([
            'url' => 'test/img6.jpg',
            'page_id' => $page->id,
            'created_at' => now()->subDay(),
        ]);
        $publicStorage->put('test/img7.jpg', 'content');
        $factory->create([
            'url' => 'test/img7.jpg',
            'page_id' => $page->id,
            'created_at' => now()->subMonths(5),
        ]);
        // files used in another models
        $publicStorage->put('test/img8.jpg', 'content');
        $factory->create([
            'url' => 'test/img8.jpg',
            'created_at' => now()->subMonths(5),
        ]);
        $factory->create([
            'url' => 'test/img8.jpg',
            'created_at' => now()->subMonths(5),
            'page_id' => $page->id,
        ]);
        $publicStorage->put('test/img9.jpg', 'content');
        if ('files' === $table) {
            $publicStorage->put(env('THUMBNAILS_FOLDER') . '/img9.jpg', 'content');
        }
        $factory->create([
            'url' => 'test/img9.jpg',
            'created_at' => now()->subMonths(5),
        ]);
        $factory->create([
            'url' => 'test/img9.jpg',
            'created_at' => now()->subMonths(5),
            'product_id' => $product->id,
        ]);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseCount($table, 7);
        $publicStorage->assertExists('test/img1.jpg');
        $publicStorage->assertExists('test/img2.jpg');
        $publicStorage->assertExists('test/img3.jpg');
        $publicStorage->assertExists('test/img6.jpg');
        $publicStorage->assertExists('test/img7.jpg');
        $publicStorage->assertMissing('test/img4.jpg');
        $publicStorage->assertMissing('test/img5.jpg');
        // files used in another models
        $publicStorage->assertExists('test/img8.jpg');
        $publicStorage->assertExists('test/img9.jpg');
        if ('files' === $table) {
            $publicStorage->assertExists(env('THUMBNAILS_FOLDER') . '/img1.jpg');
        }
        if ('files' === $table) {
            $publicStorage->assertMissing(env('THUMBNAILS_FOLDER') . '/img4.jpg');
        }
        if ('files' === $table) {
            $publicStorage->assertExists(env('THUMBNAILS_FOLDER') . '/img9.jpg');
        }
    }
}
