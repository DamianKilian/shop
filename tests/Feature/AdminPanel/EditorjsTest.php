<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Attachment;
use App\Models\File;
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
        $folder = env('ATTACHMENTS_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $size = 1024;
        $attachment = UploadedFile::fake()->create('test.txt', $size / 1024, 'text/plain');
        $name = $attachment->hashName();
        $url = $folder . "/$name";
        $urlFull = env('APP_URL') . Storage::url($url);

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-attachment', [
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
        $this->assertDatabaseHas('attachments', [
            'url' => $url,
        ]);
    }

    public function test_uploadAttachmentTwice(): void
    {
        $folder = env('ATTACHMENTS_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $size = 1024;
        $attachment = UploadedFile::fake()->create('test.txt', $size / 1024, 'text/plain');
        $name = $attachment->hashName();
        $url = $folder . "/$name";
        $urlFull = env('APP_URL') . Storage::url($url);

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-attachment', [
            'file' => $attachment,
        ]);
        $response2 = $this->actingAs($user)->post('/admin-panel/editorjs/upload-attachment', [
            'file' => $attachment,
        ]);

        $response2
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
        $this->assertEquals(2, Attachment::whereUrl($url)->get()->count());
    }

    public function test_uploadFileGallery(): void
    {
        $this->uploadFile(thumbnail: true, displayType: 'gallery');
    }

    public function test_uploadFile(): void
    {
        $this->uploadFile();
    }

    public function uploadFile($thumbnail = false, $displayType = 'image'): void
    {
        $tfolder = env('THUMBNAILS_FOLDER');
        $folder = env('IMAGES_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $name = $image->hashName();
        $url = $folder . "/$name";
        $urlFull = env('APP_URL') . Storage::url($url);

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-file', [
            'image' => $image,
            'thumbnail' => $thumbnail,
            'displayType' => $displayType,
        ]);
        $urlAbsolute = $publicStorage->path($publicStorage->files($folder)[0]);
        list($width, $height) = getimagesize($urlAbsolute);
        if ($thumbnail) {
            $turlAbsolute = $publicStorage->path($publicStorage->files($tfolder)[0]);
            list($twidth, $theight) = getimagesize($turlAbsolute);
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
        $this->assertDatabaseHas('files', [
            'url' => $url,
            'thumbnail' => (int)$thumbnail,
            'display_type' => $displayType,
        ]);
        if ($thumbnail) {
            $this->assertEquals(1, count($publicStorage->files($tfolder)));
            $this->assertEquals(sett('THUMBNAIL_MAX_SIZE'), $twidth);
            $this->assertTrue($twidth > $theight);
        }
    }

    public function test_uploadFileTwice(): void
    {
        $this->uploadFileTwice([false, false]);
    }

    public function test_uploadFileTwice2(): void
    {
        $this->uploadFileTwice([false, true]);
    }


    public function uploadFileTwice($thumbnailPresence): void
    {
        $tfolder = env('THUMBNAILS_FOLDER');
        $folder = env('IMAGES_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $name = $image->hashName();
        $url = $folder . "/$name";
        $urlFull = env('APP_URL') . Storage::url($url);

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-file', [
            'image' => $image,
            'thumbnail' => $thumbnailPresence[0],
        ]);
        if ([false, true] == $thumbnailPresence) {
            $file1 = File::first();
            $file1->thumbnail = true;
            $file1->save();
        }
        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-file', [
            'image' => $image,
            'thumbnail' => $thumbnailPresence[1],
        ]);
        $urlAbsolute = $publicStorage->path($publicStorage->files($folder)[0]);
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
        $publicStorage->assertExists($folder . '/' . $image->hashName());
        $this->assertEquals(1920, $width);
        $this->assertEquals(1000, $height);
        $this->assertEquals(1, count($publicStorage->files($folder)));
        $this->assertEquals(0, count($publicStorage->files($tfolder)));
        $this->assertEquals(2, File::whereUrl($url)->get()->count());
    }

    public function test_fetchUrl(): void
    {
        $folder = env('IMAGES_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $tempStorage = Storage::fake('temp');
        $url = 'https://fastly.picsum.photos/id/879/200/300.jpg?hmac=07llkorYxtpw0EwxaeqFKPC5woveWVLykQVnIOyiwd8';

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/fetch-url', [
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
        $this->assertDatabaseHas('files', [
            'url' => $url,
        ]);
    }
}
