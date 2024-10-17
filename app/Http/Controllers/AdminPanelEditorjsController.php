<?php

namespace App\Http\Controllers;

use App\Models\PageFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;

class AdminPanelEditorjsController extends Controller
{
    public function saveFile($request)
    {
        $name = $request->image->hashName();
        $url = "pages/$name";
        PageFile::create([
            'url' => $url,
        ]);
        $publicStorage = Storage::disk('public');
        $urlFull = env('APP_URL') . Storage::url($url);
        $urlAbsolute = $publicStorage->path($url);
        $publicStorage->put("pages", $request->image);
        Image::load($urlAbsolute)
            ->fit(Fit::Max, 1920)
            ->save();
        ImageOptimizer::optimize($urlAbsolute);
        return [
            'url' => $urlFull,
            'urlDb' => $url
        ];
    }

    public function uploadFile(Request $request)
    {
        $return = [
            'file' => [
                'url' => '',
                'urlDb' => ''
            ],
            'success' => 1,
        ];
        try {
            $return['file'] = $this->saveFile($request);
        } catch (\Throwable $th) {
            $return['success'] = 0;
        }
        return response()->json($return);
    }

    public function fetchUrl(Request $request)
    {
    }

}
