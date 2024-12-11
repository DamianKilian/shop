<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

    public function uploadAttachment(Request $request)
    {
        return response()->json($this->storeFile($request->file, displayType: 'attachment', fileType: 'attachment', thumbnail: false));
    }

    protected function getStorageFolder($fileType)
    {
        if ('attachment' === $fileType) {
            return env('ATTACHMENTS_FOLDER');
        } elseif ('image' === $fileType) {
            return env('IMAGES_FOLDER');
        }
    }

    protected function findFileInDb($hash, $fileType)
    {
        $fileInDb = null;
        if ('attachment' === $fileType) {
            $t = 'attachments';
            $select = ['url'];
        } elseif ('image' === $fileType) {
            $t = 'files';
            $select = ['url', 'thumbnail'];
        }
        $r = DB::table($t)
            ->select($select)
            ->whereHash($hash)
            ->get();
        if ($r->isEmpty()) {
            return $fileInDb;
        }
        if ('image' === $fileType) {
            $fileInDb = $r->first(function ($value, $key) {
                return 1 === $value->thumbnail;
            });
        }
        if (!$fileInDb) {
            $fileInDb = $r->first();
        }
        $urlExplode = explode('/', $fileInDb->url);
        $fileInDb->name = end($urlExplode);
        return $fileInDb;
    }

    protected function saveFile($file, $displayType, $fileType, $thumbnail)
    {
        $hash = hash_file('sha256', $file);
        $fileInDb = $this->findFileInDb($hash, $fileType);
        $publicStorage = Storage::disk('public');
        if ($fileInDb) {
            $name = $fileInDb->name;
            $url = $fileInDb->url;
        } else {
            $name = $file->hashName();
            $folder = $this->getStorageFolder($fileType);
            $url = "$folder/$name";
            $publicStorage->put($folder, $file);
            $urlAbsolute = $publicStorage->path($url);
        }
        if ('image' === $fileType) {
            File::create(['url' => $url, 'hash' => $hash, 'thumbnail' => $thumbnail, 'display_type' => $displayType]);
        } elseif ('attachment' === $fileType) {
            Attachment::create(['url' => $url, 'hash' => $hash]);
        }
        if ('image' === $fileType) {
            if (!$fileInDb) {
                Image::load($urlAbsolute)
                    ->fit(Fit::Max, 1920)
                    ->save();
                ImageOptimizer::optimize($urlAbsolute);
            }
            if ($thumbnail && (!$fileInDb || (0 === $fileInDb->thumbnail))) {
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
            'url' => env('APP_URL') . Storage::url($url),
            'urlDb' => $url,
            'size' => $file->getSize(),
        ];
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
            $fileData['file'] = $this->saveFile($file, $displayType, $fileType, $thumbnail);
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
