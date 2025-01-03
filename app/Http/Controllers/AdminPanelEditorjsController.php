<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPanelEditorjsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function uploadAttachment(Request $request)
    {
        return response()->json($this->storeFile($request->file, displayType: 'attachment', fileType: 'attachment', thumbnail: false));
    }

    protected function storeFile($file, $displayType, $fileType, $thumbnail = false)
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
            $fileData['file'] = FileService::saveFile($file, $displayType, $fileType, $thumbnail);
        } catch (\Throwable $th) {
            $fileData['success'] = 0;
        }
        return $fileData;
    }

    public function uploadFile(Request $request)
    {
        $thumbnail = !!$request->thumbnail;
        $displayType = $request->displayType ?: 'image';
        return response()->json($this->storeFile($request->image, $displayType, 'image', $thumbnail));
    }

    public function fetchUrl(Request $request)
    {
        $tmpFile = Str::random(40);
        $tempStorage = Storage::disk('temp');
        $tempStorage->put($tmpFile, file_get_contents($request->url));
        $image = new UploadedFile($tempStorage->path($tmpFile), 'fetchUrlFile');
        $thumbnail = !!$request->thumbnail;
        $displayType = $request->displayType ?: 'image';
        $fileData = $this->storeFile($image, $displayType, 'image', $thumbnail);
        $tempStorage->delete($tmpFile);
        return response()->json($fileData);
    }
}
