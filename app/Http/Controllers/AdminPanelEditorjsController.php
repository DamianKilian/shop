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

    protected function getStorageFolder($type, $fileType)
    {
        if ('product' === $type) {
            if ('attachment' === $fileType) {
                return env('PRODUCT_ATTACHMENT_FOLDER');
            } elseif ('image' === $fileType) {
                return env('PRODUCT_IMAGE_FOLDER');
            }
        } elseif ('page' === $type) {
            if ('attachment' === $fileType) {
                return env('PAGE_ATTACHMENT_FOLDER');
            } elseif ('image' === $fileType) {
                return env('PAGE_IMAGE_FOLDER');
            }
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

    public function saveFile($file, $type, $fileType)
    {
        $name = $file->hashName();
        $folder = $this->getStorageFolder($type, $fileType);
        $url = "$folder/$name";
        $this->saveFileInDb($type, $fileType, $url);
        $publicStorage = Storage::disk('public');
        $urlFull = env('APP_URL') . Storage::url($url);
        $urlAbsolute = $publicStorage->path($url);
        $publicStorage->put($folder, $file);
        if ('image' === $fileType) {
            Image::load($urlAbsolute)
                ->fit(Fit::Max, 1920)
                ->save();
            ImageOptimizer::optimize($urlAbsolute);
        }
        return [
            'url' => $urlFull,
            'urlDb' => $url,
            'size' => $file->getSize(),
        ];
    }

    public function storeFile($file, $type, $fileType = 'image')
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
            $fileData['file'] = $this->saveFile($file, $type, $fileType);
        } catch (\Throwable $th) {
            $fileData['success'] = 0;
        }
        return $fileData;
    }

    public function uploadFile(Request $request, $type = 'page')
    {
        return response()->json($this->storeFile($request->image, $type));
    }

    public function fetchUrl(Request $request, $type = 'page')
    {
        $tmpFile = Str::random(40);
        $tempStorage = Storage::disk('temp');
        $tempStorage->put($tmpFile, file_get_contents($request->url));
        $image = new UploadedFile($tempStorage->path($tmpFile), 'fetchUrlFile');
        $fileData = $this->storeFile($image, $type);
        $tempStorage->delete($tmpFile);
        return response()->json($fileData);
    }
}
