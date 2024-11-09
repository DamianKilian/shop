<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\PageAttachment;
use App\Models\PageFile;
use App\Models\Product;
use App\Models\ProductAttachment;
use App\Models\ProductFile;
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
            'last_used' => now()->subMonths(22),
        ]);

        $this->artisan('prune:suggestions')->assertExitCode(0);
        $this->assertDatabaseCount('suggestions', 5);
    }

    public function test_prune_files(): void
    {
        $this->prune_files(PageFile::factory(), 'prune:pageFiles', 'page_files', 'page');
        $this->prune_files(ProductFile::factory(), 'prune:productFiles', 'product_files', 'product');
        $this->prune_files(PageAttachment::factory(), 'prune:pageAttachments', 'page_attachments', 'page');
        $this->prune_files(ProductAttachment::factory(), 'prune:productAttachments', 'product_attachments', 'product');
    }

    public function prune_files($factory, $command, $table, $type): void
    {
        $publicStorage = Storage::fake('public');
        // 5 pageFiles
        $publicStorage->put('test/img1.jpg', 'content');
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
        $factory->create([
            'url' => 'test/img4.jpg',
            'created_at' => now()->subWeeks(5),
        ]);
        $publicStorage->put('test/img5.jpg', 'content');
        $factory->create([
            'url' => 'test/img5.jpg',
            'created_at' => now()->subWeeks(22),
        ]);
        // 7 pageFiles (5 + 2)
        $publicStorage->put('test/img6.jpg', 'content');
        $publicStorage->put('test/img7.jpg', 'content');

        if ('page' === $type) {
            $page = Page::factory()->create();
            $factory->create([
                'url' => 'test/img6.jpg',
                'created_at' => now()->subDay(),
                'page_id' => $page->id,
            ]);
            $factory->create([
                'url' => 'test/img7.jpg',
                'created_at' => now()->subWeeks(22),
                'page_id' => $page->id,
            ]);
        } elseif ('product' === $type) {
            $category = Category::factory()->create();
            $product = Product::factory()->create([
                'category_id' => $category->id,
            ]);
            $factory->create([
                'url' => 'products/img6.jpg',
                'created_at' => now()->subDay(),
                'product_id' => $product->id,
            ]);
            $factory->create([
                'url' => 'products/img7.jpg',
                'created_at' => now()->subWeeks(22),
                'product_id' => $product->id,
            ]);
        }

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseCount($table, 5);
        $publicStorage->assertExists('test/img1.jpg');
        $publicStorage->assertExists('test/img2.jpg');
        $publicStorage->assertExists('test/img3.jpg');
        $publicStorage->assertExists('test/img6.jpg');
        $publicStorage->assertExists('test/img7.jpg');
        $publicStorage->assertMissing('test/img4.jpg');
        $publicStorage->assertMissing('test/img5.jpg');
    }
}
