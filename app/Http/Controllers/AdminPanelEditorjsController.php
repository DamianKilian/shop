<?php

namespace App\Http\Controllers;

use App\Models\PageFile;
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

    public function saveFile($image, $type)
    {
        $name = $image->hashName();
        $folder = $type . 's';
        $url = "$folder/$name";
        if ('product' === $type) {
            ProductFile::create([
                'url' => $url,
            ]);
        } elseif ('page' === $type) {
            PageFile::create([
                'url' => $url,
            ]);
        }
        $publicStorage = Storage::disk('public');
        $urlFull = env('APP_URL') . Storage::url($url);
        $urlAbsolute = $publicStorage->path($url);
        $publicStorage->put($folder, $image);
        Image::load($urlAbsolute)
            ->fit(Fit::Max, 1920)
            ->save();
        ImageOptimizer::optimize($urlAbsolute);
        return [
            'url' => $urlFull,
            'urlDb' => $url
        ];
    }

    public function storeFile($file, $type)
    {
        $fileData = [
            'file' => [
                'url' => '',
                'urlDb' => ''
            ],
            'success' => 1,
        ];
        try {
            $fileData['file'] = $this->saveFile($file, $type);
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
