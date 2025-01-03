<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public static function getStorageFolder($fileType)
    {
        if ('attachment' === $fileType) {
            return env('ATTACHMENTS_FOLDER');
        } elseif ('image' === $fileType) {
            return env('IMAGES_FOLDER');
        }
    }

    public static function findFileInDb($url, $fileType)
    {
        $t = 'image' === $fileType ? 'files' : 'attachments';
        return DB::table($t)
            ->select(['url'])
            ->whereUrl($url)
            ->first();
    }

    public static function findThumbnailInDb($urlThumbnail)
    {
        return DB::table('files')
            ->select(['url_thumbnail'])
            ->whereUrlThumbnail($urlThumbnail)
            ->first();
    }

    public static function finalWidth($file, $fileType, $maxWidth)
    {
        if ('image' !== $fileType) {
            return;
        }
        $width = $file->dimensions()[0];
        $width = $width > $maxWidth ? $maxWidth : $width;
        return $width;
    }

    public static function saveFile($file, $displayType, $fileType, $thumbnail, $position = null, $maxWidth = 1920, $thumbnailMaxSize = null, $productId = null)
    {
        $hash = hash_file('sha256', $file);
        $folder = self::getStorageFolder($fileType);
        if ($extension = $file->guessExtension()) {
            $extension = '.' . $extension;
        }
        $name = $hash . '_' . self::finalWidth($file, $fileType, $maxWidth) . $extension;
        $url = "$folder/$name";
        $urlThumbnail = null;
        $fileInDb = self::findFileInDb($url, $fileType);
        $publicStorage = Storage::disk('public');
        if (!$fileInDb) {
            $publicStorage->putFileAs($folder, $file, $name);
            if ('image' === $fileType) {
                $urlAbsolute = $publicStorage->path($url);
                Image::load($urlAbsolute)
                    ->fit(Fit::Max, $maxWidth)
                    ->save();
                ImageOptimizer::optimize($urlAbsolute);
            }
        }
        if ('image' === $fileType) {
            $thumbnailMaxSize = $thumbnailMaxSize ?: sett('THUMBNAIL_MAX_SIZE');
            $tname = $hash . '_' . $thumbnailMaxSize . $extension;
            $tfolder = env('THUMBNAILS_FOLDER');
            $turl = "$tfolder/$tname";
            $urlThumbnail = $thumbnail ? $turl : null;
            if ($urlThumbnail) {
                $thumbnailInDb = self::findThumbnailInDb($urlThumbnail);
                if (!$thumbnailInDb) {
                    $publicStorage->putFileAs($tfolder, $file, $tname);
                    $turlAbsolute = $publicStorage->path($urlThumbnail);
                    Image::load($turlAbsolute)
                        ->fit(Fit::Contain, $thumbnailMaxSize, $thumbnailMaxSize)
                        ->save();
                    ImageOptimizer::optimize($turlAbsolute);
                }
            }
            File::create(['url' => $url, 'position' => $position, 'url_thumbnail' => $urlThumbnail, 'display_type' => $displayType, 'product_id' => $productId]);
        } elseif ('attachment' === $fileType) {
            Attachment::create(['url' => $url, 'hash' => $hash, 'product_id' => $productId]);
        }
        return [
            'url' => env('APP_URL') . Storage::url($url),
            'urlDb' => $url,
            'size' => $file->getSize(),
        ];
    }
}
