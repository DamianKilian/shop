<?php

namespace App\Http\Controllers;

use App\Models\PageFile;
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

    public function saveFile($image)
    {
        $name = $image->hashName();
        $url = "pages/$name";
        PageFile::create([
            'url' => $url,
        ]);
        $publicStorage = Storage::disk('public');
        $urlFull = env('APP_URL') . Storage::url($url);
        $urlAbsolute = $publicStorage->path($url);
        $publicStorage->put("pages", $image);
        Image::load($urlAbsolute)
            ->fit(Fit::Max, 1920)
            ->save();
        ImageOptimizer::optimize($urlAbsolute);
        return [
            'url' => $urlFull,
            'urlDb' => $url
        ];
    }

    public function storeFile($image)
    {
        $fileData = [
            'file' => [
                'url' => '',
                'urlDb' => ''
            ],
            'success' => 1,
        ];
        try {
            $fileData['file'] = $this->saveFile($image);
        } catch (\Throwable $th) {
            $fileData['success'] = 0;
        }
        return $fileData;
    }

    public function uploadFile(Request $request)
    {
        return response()->json($this->storeFile($request->image));
    }

    public function fetchUrl(Request $request)
    {
        $tmpFile = Str::random(40);
        $tempStorage = Storage::disk('temp');
        $tempStorage->put($tmpFile, file_get_contents($request->url));
        $image = new UploadedFile($tempStorage->path($tmpFile), 'fetchUrlFile');
        $fileData = $this->storeFile($image);
        $tempStorage->delete($tmpFile);
        return response()->json($fileData);
    }
}
