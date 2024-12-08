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

    public function test_uploadAttachment(): void
    {
        $this->uploadAttachment('/admin-panel/editorjs/upload-attachment', 'page_attachments');
        $this->uploadAttachment('/admin-panel/editorjs/upload-attachment/product', 'product_attachments');
    }

    public function uploadAttachment($uploadAttachmentUrl, $table): void
    {
        $folder = env('ATTACHMENTS_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $size = 1024;
        $attachment = UploadedFile::fake()->create('test.txt', $size / 1024, 'text/plain');
        $name = $attachment->hashName();
        $url = $folder . "/$name";
        $urlFull = env('APP_URL') . Storage::url($url);

        $response = $this->actingAs($user)->post($uploadAttachmentUrl, [
            'file' => $attachment,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'file' => [
                    'url' => $urlFull,
                    'urlDb' => $url,
                    'size' => $size,
                ],
                'success' => 1,
            ]);
        $publicStorage->assertExists($folder . '/' . $attachment->hashName());
        $this->assertEquals(1, count($publicStorage->files($folder)));
        $this->assertDatabaseHas($table, [
            'url' => $url,
        ]);
    }

    public function test_uploadFile(): void
    {
        $this->uploadFile('/admin-panel/editorjs/upload-file', 'page_files', $thumbnail = true);
        $this->uploadFile('/admin-panel/editorjs/upload-file/product', 'product_files');
    }

    public function uploadFile($uploadFileUrl, $table, $thumbnail = false): void
    {
        $tfolder = env('THUMBNAILS_FOLDER');
        $folder = env('IMAGES_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $name = $image->hashName();
        $url = $folder . "/$name";
        $urlFull = env('APP_URL') . Storage::url($url);

        $response = $this->actingAs($user)->post($uploadFileUrl, [
            'image' => $image,
            'thumbnail' => $thumbnail
        ]);
        $urlAbsolute = $publicStorage->path($publicStorage->files($folder)[0]);
        list($width, $height) = getimagesize($urlAbsolute);
        if ($thumbnail) {
            $turlAbsolute = $publicStorage->path($publicStorage->files($tfolder)[0]);
            list($twidth, $theight) = getimagesize($turlAbsolute);

            $this->assertEquals(1, count($publicStorage->files($tfolder)));
            $this->assertEquals(sett('THUMBNAIL_MAX_SIZE'), $twidth);
            $this->assertTrue($twidth > $theight);
        }

        $response
            ->assertStatus(200)
            ->assertJson([
                'file' => [
                    'url' => $urlFull,
                    'urlDb' => $url
                ],
                'success' => 1,
            ]);
        $publicStorage->assertExists($folder . '/' . $image->hashName());
        $this->assertEquals(1920, $width);
        $this->assertEquals(1000, $height);
        $this->assertEquals(1, count($publicStorage->files($folder)));
        $this->assertDatabaseHas($table, [
            'url' => $url,
        ]);
    }

    public function test_fetchUrl(): void
    {
        $this->fetchUrl('/admin-panel/editorjs/fetch-url', 'page_files');
        $this->fetchUrl('/admin-panel/editorjs/fetch-url/product', 'product_files');
    }

    public function fetchUrl($fetchUrl, $table): void
    {
        $folder = env('IMAGES_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $tempStorage = Storage::fake('temp');
        $url = 'https://fastly.picsum.photos/id/879/200/300.jpg?hmac=07llkorYxtpw0EwxaeqFKPC5woveWVLykQVnIOyiwd8';

        $response = $this->actingAs($user)->post($fetchUrl, [
            'url' => $url,
        ]);
        $url = $publicStorage->files($folder)[0];
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
        $this->assertEquals(1, count($publicStorage->files($folder)));
        $this->assertEquals(0, count($tempStorage->files()));
        $this->assertDatabaseHas($table, [
            'url' => $url,
        ]);
    }
}
