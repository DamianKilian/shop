<?php

namespace App\Http\Controllers;

use App\Models\PageAttachment;
use App\Models\PageFile;
use App\Models\ProductAttachment;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Str;

class AdminPanelEditorjsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function uploadAttachment(Request $request, $type = 'page')
    {
        return response()->json($this->storeFile($request->file, $type, 'attachment'));
    }

    protected function getStorageFolder($fileType)
    {
        if ('attachment' === $fileType) {
            return env('ATTACHMENTS_FOLDER');
        } elseif ('image' === $fileType) {
            return env('IMAGES_FOLDER');
        }
    }

    protected function saveFileInDb($type, $fileType, $url)
    {
        if ('product' === $type) {
            if ('attachment' === $fileType) {
                ProductAttachment::create(['url' => $url,]);
            } elseif ('image' === $fileType) {
                ProductFile::create(['url' => $url,]);
            }
        } elseif ('page' === $type) {
            if ('attachment' === $fileType) {
                PageAttachment::create(['url' => $url,]);
            } elseif ('image' === $fileType) {
                PageFile::create(['url' => $url,]);
            }
        }
    }

    public function saveFile($file, $type, $fileType, $thumbnail)
    {
        $folder = $this->getStorageFolder($fileType);
        $name = $file->hashName();
        $url = "$folder/$name";
        $urlFull = env('APP_URL') . Storage::url($url);
        $this->saveFileInDb($type, $fileType, $url);
        $publicStorage = Storage::disk('public');
        $urlAbsolute = $publicStorage->path($url);
        $publicStorage->put($folder, $file);
        if ('image' === $fileType) {
            Image::load($urlAbsolute)
                ->fit(Fit::Max, 1920)
                ->save();
            ImageOptimizer::optimize($urlAbsolute);
            if ($thumbnail) {
                $tfolder = env('THUMBNAILS_FOLDER');
                $turl = "$tfolder/$name";
                $turlAbsolute = $publicStorage->path($turl);
                $publicStorage->put($tfolder, $file);
                Image::load($turlAbsolute)
                    ->fit(Fit::Contain, sett('THUMBNAIL_MAX_SIZE'), sett('THUMBNAIL_MAX_SIZE'))
                    ->save();
                ImageOptimizer::optimize($turlAbsolute);
            }
        }
        return [
            'url' => $urlFull,
            'urlDb' => $url,
            'size' => $file->getSize(),
        ];
    }

    public function storeFile($file, $type, $fileType, $thumbnail = false)
    {
        $fileData = [
            'file' => [
                'url' => '',
                'urlDb' => '',
                'size' => 0,
            ],
            'success' => 1,
        ];
        try {
            $fileData['file'] = $this->saveFile($file, $type, $fileType, $thumbnail);
        } catch (\Throwable $th) {
            $fileData['success'] = 0;
        }
        return $fileData;
    }

    public function uploadFile(Request $request, $type = 'page')
    {
        $thumbnail = !!$request->thumbnail;
        return response()->json($this->storeFile($request->image, $type, 'image', $thumbnail));
    }

    public function fetchUrl(Request $request, $type = 'page')
    {
        $tmpFile = Str::random(40);
        $tempStorage = Storage::disk('temp');
        $tempStorage->put($tmpFile, file_get_contents($request->url));
        $image = new UploadedFile($tempStorage->path($tmpFile), 'fetchUrlFile');
        $thumbnail = !!$request->thumbnail;
        $fileData = $this->storeFile($image, $type, 'image', $thumbnail);
        $tempStorage->delete($tmpFile);
        return response()->json($fileData);
    }
}
