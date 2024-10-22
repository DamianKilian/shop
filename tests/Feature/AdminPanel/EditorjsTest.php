<?php

namespace Tests\Feature\AdminPanel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EditorjsTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploadFile(): void
    {
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $name = $image->hashName();
        $url = "pages/$name";
        $urlFull = env('APP_URL') . Storage::url($url);

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-file', [
            'image' => $image,
        ]);
        $urlAbsolute = $publicStorage->path($publicStorage->files('pages')[0]);
        list($width, $height) = getimagesize($urlAbsolute);

        $response
            ->assertStatus(200)
            ->assertJson([
                'file' => [
                    'url' => $urlFull,
                    'urlDb' => $url
                ],
                'success' => 1,
            ]);
        $publicStorage->assertExists('pages/' . $image->hashName());
        $this->assertEquals(1920, $width);
        $this->assertEquals(1000, $height);
        $this->assertEquals(1, count($publicStorage->files('pages')));
        $this->assertDatabaseHas('page_files', [
            'url' => $url,
        ]);
    }

    public function test_fetchUrl(): void
    {
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $tempStorage = Storage::fake('temp');
        $url = 'https://fastly.picsum.photos/id/879/200/300.jpg?hmac=07llkorYxtpw0EwxaeqFKPC5woveWVLykQVnIOyiwd8';

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/fetch-url', [
            'url' => $url,
        ]);
        $url = $publicStorage->files('pages')[0];
        $urlFull = env('APP_URL') . Storage::url($url);

        $response
            ->assertStatus(200)
            ->assertJson([
                'file' => [
                    'url' => $urlFull,
                    'urlDb' => $url
                ],
                'success' => 1,
            ]);
        $this->assertEquals(1, count($publicStorage->files('pages')));
        $this->assertEquals(0, count($tempStorage->files()));
        $this->assertDatabaseHas('page_files', [
            'url' => $url,
        ]);
    }
}
