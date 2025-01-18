<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Attachment;
use App\Models\File;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EditorjsTest extends TestCase
{
    use RefreshDatabase;


    protected function getFileData($fileType, $file, $maxWidth = 1920)
    {
        $hash = hash_file(env('HASH_FILE_ALGO'), $file);
        $folder = FileService::getStorageFolder($fileType);
        if ($extension = $file->guessExtension()) {
            $extension = '.' . $extension;
        }
        $name = FileService::hyphenedName($file) . '-' . $hash . '_' . FileService::finalWidth($file, $fileType, $maxWidth) . $extension;
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
        $publicStorage = Storage::fake('public');
        $size = 1024;
        $attachment = UploadedFile::fake()->create('test.txt', $size / 1024, 'text/plain');
        $fileData = $this->getFileData('attachment', $attachment);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/editorjs/upload-attachment', [
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
        $publicStorage = Storage::fake('public');
        $size = 1024;
        $attachment = UploadedFile::fake()->create('test.txt', $size / 1024, 'text/plain');
        $fileData = $this->getFileData('attachment', $attachment);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/editorjs/upload-attachment', [
            'file' => $attachment,
        ]);
        $response2 = $this->actingAs(parent::getAdmin())->post('/admin-panel/editorjs/upload-attachment', [
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
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $fileData = $this->getFileData('image', $image);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/editorjs/upload-file', [
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
        $publicStorage = Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg', 3840, 2000);
        $fileData = $this->getFileData('image', $image);
        $folder = $fileData['folder'];
        $url = $fileData['url'];
        $urlFull = $fileData['urlFull'];

        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/editorjs/upload-file', [
            'image' => $image,
            'thumbnail' => $thumbnailPresence[0],
        ]);
        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/editorjs/upload-file', [
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
        $publicStorage = Storage::fake('public');
        $tempStorage = Storage::fake('temp');
        $url = 'https://fastly.picsum.photos/id/879/200/300.jpg?hmac=07llkorYxtpw0EwxaeqFKPC5woveWVLykQVnIOyiwd8';

        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/editorjs/fetch-url', [
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

    public function test_img_alt_tag_imageExternal(): void
    {
        $title = 'title';
        $caption = 'caption';
        $body_prod = '{"time":1736007596087,"blocks":[{"id":"kJ3A55ZNBS","type":"imageExternal","data":{"file":{"url":"https://img.stablecog.com/insecure/256w/aHR0cHM6Ly9iLnN0YWJsZWNvZy5jb20vOGMzZTY3YjctYWY5ZC00NDljLTg2ZTItZGMyNTc0NjFhNDJiLmpwZWc.webp"},"caption":"'
            . $caption . '","withBorder":false,"withBackground":false,"stretched":false}},{"id":"9J4hxsUtTm","type":"imageExternal","data":{"file":{"url":"https://img.stablecog.com/insecure/256w/aHR0cHM6Ly9iLnN0YWJsZWNvZy5jb20vOGMzZTY3YjctYWY5ZC00NDljLTg2ZTItZGMyNTc0NjFhNDJiLmpwZWc.webp"},"caption":"","withBorder":false,"withBackground":false,"stretched":false}}],"version":"2.30.6"}';
        Page::factory()->create([
            'title' => $title,
            'slug' => $title,
            'body_prod' => $body_prod,
            'active' => 1,
        ]);

        $response = $this->get('/' . $title);

        $response->assertSeeInOrder(['alt="caption"', ('alt="' . $title . '"')], false);
    }

    public function test_img_alt_tag_image(): void
    {
        $title = 'title';
        $caption = 'caption';
        $body_prod = '{"time":1736008667803,"blocks":[{"id":"avpPQl23xE","type":"image","data":{"caption":"'
            . $caption . '","withBorder":false,"withBackground":false,"stretched":false,"file":{"url":"http://localhost:8080/storage/images/dog-4561706f_225.jpg","urlDb":"images/dog-4561706f_225.jpg","size":7059}}},{"id":"n5-FT0dNJB","type":"image","data":{"caption":"","withBorder":false,"withBackground":false,"stretched":false,"file":{"url":"http://localhost:8080/storage/images/dog-4561706f_225.jpg","urlDb":"images/dog-4561706f_225.jpg","size":7059}}}],"version":"2.30.6"}';
        Page::factory()->create([
            'title' => $title,
            'slug' => $title,
            'body_prod' => $body_prod,
            'active' => 1,
        ]);

        $response = $this->get('/' . $title);

        $response->assertSeeInOrder(['alt="caption"', ('alt="' . $title . '"')], false);
    }

    public function test_img_alt_tag_gallery_standard(): void
    {
        $title = 'title';
        $caption = 'caption';
        $body_prod = '{"time":1736009854274,"blocks":[{"id":"uBv5pX0-0S","type":"gallery","data":{"items":[{"url":"http://localhost:8080/storage/images/dog-4561706f_225.jpg","caption":"'
            . $caption . '"},{"url":"http://localhost:8080/storage/images/dog-4561706f_225.jpg","caption":""}],"config":"standard","countItemEachRow":"1"}}],"version":"2.30.6"}';
        Page::factory()->create([
            'title' => $title,
            'slug' => $title,
            'body_prod' => $body_prod,
            'active' => 1,
        ]);

        $response = $this->get('/' . $title);

        $response->assertSeeInOrder(['alt="caption"', ('alt="' . $title . '"')], false);
    }

    public function test_img_alt_tag_gallery_carousel(): void
    {
        $title = 'title';
        $caption = 'caption';
        $body_prod = '{"time":1736010019007,"blocks":[{"id":"uBv5pX0-0S","type":"gallery","data":{"items":[{"url":"http://localhost:8080/storage/images/dog-4561706f_225.jpg","caption":"'
            . $caption . '"},{"url":"http://localhost:8080/storage/images/dog-4561706f_225.jpg","caption":""}],"config":"carousel","countItemEachRow":"1"}}],"version":"2.30.6"}';
        Page::factory()->create([
            'title' => $title,
            'slug' => $title,
            'body_prod' => $body_prod,
            'active' => 1,
        ]);

        $response = $this->get('/' . $title);

        $response->assertSeeInOrder(['alt="caption"', ('alt="' . $title . '"')], false);
    }
}
