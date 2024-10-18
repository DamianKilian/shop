<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageFile;
use App\Models\Suggestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommandTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_prune_pageFiles(): void
    {
        $publicStorage = Storage::fake('public');
        // 5 pageFiles
        $publicStorage->put('pages/img1.jpg', 'content');
        PageFile::factory()->create([
            'url' => 'pages/img1.jpg',
        ]);
        $publicStorage->put('pages/img2.jpg', 'content');
        PageFile::factory()->create([
            'url' => 'pages/img2.jpg',
            'created_at' => now()->subDay(),
        ]);
        $publicStorage->put('pages/img3.jpg', 'content');
        PageFile::factory()->create([
            'url' => 'pages/img3.jpg',
            'created_at' => now()->subDays(5),
        ]);
        $publicStorage->put('pages/img4.jpg', 'content');
        PageFile::factory()->create([
            'url' => 'pages/img4.jpg',
            'created_at' => now()->subWeeks(5),
        ]);
        $publicStorage->put('pages/img5.jpg', 'content');
        PageFile::factory()->create([
            'url' => 'pages/img5.jpg',
            'created_at' => now()->subWeeks(22),
        ]);
        // 7 pageFiles (5 + 2)
        $page = Page::factory()->create();
        $publicStorage->put('pages/img6.jpg', 'content');
        PageFile::factory()->create([
            'url' => 'pages/img6.jpg',
            'created_at' => now()->subDay(),
            'page_id' => $page->id,
        ]);
        $publicStorage->put('pages/img7.jpg', 'content');
        PageFile::factory()->create([
            'url' => 'pages/img7.jpg',
            'created_at' => now()->subWeeks(22),
            'page_id' => $page->id,
        ]);

        $this->artisan('prune:pageFiles')->assertExitCode(0);
        $this->assertDatabaseCount('page_files', 5);
        $publicStorage->assertExists('pages/img1.jpg');
        $publicStorage->assertExists('pages/img2.jpg');
        $publicStorage->assertExists('pages/img3.jpg');
        $publicStorage->assertExists('pages/img6.jpg');
        $publicStorage->assertExists('pages/img7.jpg');
        $publicStorage->assertMissing('pages/img4.jpg');
        $publicStorage->assertMissing('pages/img5.jpg');
    }
}
