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
            return config('my.attachments_folder');
        } elseif ('image' === $fileType) {
            return config('my.images_folder');
        }
    }

    public static function findFileInDb($url, $fileType)
    {
        $t = 'image' === $fileType ? 'files' : 'attachments';
        return DB::table($t)
            ->select(['url', 'data'])
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

    public static function hyphenedName($file)
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return preg_replace('/\\s+/', "-", trim(preg_replace('/[^A-Za-z0-9]/', " ", $name)));
    }

    public static function saveFile($file, $displayType, $fileType, $thumbnail, $position = null, $maxWidth = 1920, $thumbnailMaxSize = null, $productId = null)
    {
        $hash = hash_file(config('my.hash_file_algo'), $file);
        $folder = self::getStorageFolder($fileType);
        if ($extension = $file->guessExtension()) {
            $extension = '.' . $extension;
        }
        $hyphenedName = self::hyphenedName($file);
        $name = $hyphenedName . '-' . $hash . '_' . self::finalWidth($file, $fileType, $maxWidth) . $extension;
        $url = "$folder/$name";
        $urlThumbnail = null;
        $fileInDb = self::findFileInDb($url, $fileType);
        $publicStorage = Storage::disk('public');
        if (!$fileInDb) {
            $publicStorage->putFileAs($folder, $file, $name);
            if ('image' === $fileType) {
                $urlAbsolute = $publicStorage->path($url);
                $image = Image::load($urlAbsolute)
                    ->fit(Fit::Max, $maxWidth)
                    ->save();
                ImageOptimizer::optimize($urlAbsolute);
                $data = json_encode(['width' => $image->getWidth(), 'height' => $image->getHeight()]);
            }
        } else {
            if ('image' === $fileType) {
                $data = $fileInDb->data;
            }
        }
        if ('image' === $fileType) {
            $thumbnailMaxSize = $thumbnailMaxSize ?: sett('THUMBNAIL_MAX_SIZE');
            $tname = $hyphenedName . '-' . $hash . '_' . $thumbnailMaxSize . $extension;
            $tfolder = config('my.thumbnails_folder');
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
            File::create(['url' => $url, 'data' => $data, 'position' => $position, 'url_thumbnail' => $urlThumbnail, 'display_type' => $displayType, 'product_id' => $productId]);
        } elseif ('attachment' === $fileType) {
            Attachment::create(['url' => $url, 'hash' => $hash, 'product_id' => $productId]);
        }
        return [
            'url' => config('app.url') . Storage::url($url),
            'urlDb' => $url,
            'size' => $file->getSize(),
        ];
    }
}
