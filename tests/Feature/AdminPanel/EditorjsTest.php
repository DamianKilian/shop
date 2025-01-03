<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Attachment;
use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EditorjsTest extends TestCase
{
    use RefreshDatabase;


    protected function getFileData($fileType, $file, $maxWidth = 1920)
    {
        $hash = hash_file('sha256', $file);
        $folder = FileService::getStorageFolder($fileType);
        if ($extension = $file->guessExtension()) {
            $extension = '.' . $extension;
        }
        $name = $hash . '_' . FileService::finalWidth($file, $fileType, $maxWidth) . $extension;
        $url = "$folder/$name";
        return [
            'name' => $name,
            'folder' => $folder,
            'url' => $url,
            'urlFull' => env('APP_URL') . Storage::url($url),
        ];
    }

    public function test_uploadAttachment(): void
    {
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $size = 1024;
        $attachment = UploadedFile::fake()->create('test.txt', $size / 1024, 'text/plain');
        $fileData = $this->getFileData('attachment', $attachment);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

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
        $publicStorage->assertExists($folder . '/' . $fileData['name']);
        $this->assertEquals(1, count($publicStorage->files($folder)));
        $this->assertDatabaseHas('attachments', [
            'url' => $url,
        ]);
    }

    public function test_uploadAttachment_twice(): void
    {
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $size = 1024;
        $attachment = UploadedFile::fake()->create('test.txt', $size / 1024, 'text/plain');
        $fileData = $this->getFileData('attachment', $attachment);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

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
        $publicStorage->assertExists($folder . '/' . $fileData['name']);
        $this->assertEquals(1, count($publicStorage->files($folder)));
        $this->assertEquals(2, Attachment::whereUrl($url)->get()->count());
    }

    public function test_uploadFile_gallery(): void
    {
        $this->uploadFile(thumbnail: true, displayType: 'gallery');
    }

    public function test_uploadFile(): void
    {
        $this->uploadFile();
    }

    public function uploadFile($thumbnail = false, $displayType = 'image'): void
    {
        // $tfolder = env('THUMBNAILS_FOLDER');
        // $folder = env('IMAGES_FOLDER');
        // $user = User::factory()->create();
        // $publicStorage = Storage::fake('public');
        // $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        // $name = $image->hashName();
        // $url = $folder . "/$name";
        // $urlFull = env('APP_URL') . Storage::url($url);

        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $fileData = $this->getFileData('image', $image);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-file', [
            'image' => $image,
            'thumbnail' => $thumbnail,
            'displayType' => $displayType,
        ]);
        $urlAbsolute = $publicStorage->path($publicStorage->files($folder)[0]);
        list($width, $height) = getimagesize($urlAbsolute);
        $turl = null;
        if ($thumbnail) {
            $tfolder = env('THUMBNAILS_FOLDER');
            $turl = $publicStorage->files($tfolder)[0];
            $turlAbsolute = $publicStorage->path($turl);
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
        $publicStorage->assertExists($folder . '/' . $fileData['name']);
        $this->assertEquals(1920, $width);
        $this->assertEquals(1000, $height);
        $this->assertEquals(1, count($publicStorage->files($folder)));
        $this->assertDatabaseHas('files', [
            'url' => $url,
            'url_thumbnail' => $turl,
            'display_type' => $displayType,
        ]);
        if ($thumbnail) {
            $this->assertEquals(1, count($publicStorage->files($tfolder)));
            $this->assertEquals(sett('THUMBNAIL_MAX_SIZE'), $twidth);
            $this->assertTrue($twidth > $theight);
        }
    }

    public function test_uploadFile_twice(): void
    {
        $this->uploadFile_twice([false, false]);
    }

    public function test_uploadFile_twice2(): void
    {
        $this->uploadFile_twice([false, true]);
    }

    public function test_uploadFile_twice3(): void
    {
        $this->uploadFile_twice([true, true]);
    }

    public function uploadFile_twice($thumbnailPresence): void
    {
        $tfolder = env('THUMBNAILS_FOLDER');
        $user = User::factory()->create();
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $fileData = $this->getFileData('image', $image);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

        $response = $this->actingAs($user)->post('/admin-panel/editorjs/upload-file', [
            'image' => $image,
            'thumbnail' => $thumbnailPresence[0],
        ]);
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
        $publicStorage->assertExists($folder . '/' . $fileData['name']);
        $this->assertEquals(1920, $width);
        $this->assertEquals(1000, $height);
        $this->assertEquals(1, count($publicStorage->files($folder)));
        if ([false, false] == $thumbnailPresence) {
            $this->assertEquals(0, count($publicStorage->files($tfolder)));
        } else {
            $this->assertEquals(1, count($publicStorage->files($tfolder)));
        }
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
