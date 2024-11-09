<?php

use App\Models\PageAttachment;
use App\Models\PageFile;
use App\Models\ProductAttachment;
use App\Models\ProductFile;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();




// Schedules:

Artisan::command('prune:temp', function () {
    $tempStorage = Storage::disk('temp');
    $paths = $tempStorage->files();
    $month = 2592000;
    $time = time();
    foreach ($paths as $path) {
        if ($time > $month + filemtime($tempStorage->path($path))) {
            $tempStorage->delete($path);
        }
    }
    $this->comment('Old temp files removed');
})->purpose('Remove unused suggestions')->daily();

Artisan::command('prune:suggestions', function () {
    DB::table('suggestions')
        ->where('last_used', '<=', now()->subYear())
        ->delete();
    $this->comment('Unused suggestions removed');
})->purpose('Remove unused suggestions')->daily();

Artisan::command('prune:pageFiles', function () {
    $pageFiles = DB::table('page_files')
        ->where('page_id', null)
        ->where('created_at', '<=', now()->subMonth())
        ->get();
    $publicStorage = Storage::disk('public');
    foreach ($pageFiles as $pageFile) {
        $publicStorage->delete($pageFile->url);
    }
    PageFile::destroy($pageFiles->pluck('id'));
    $this->comment('Unused pageFiles removed');
})->purpose('Remove unused pageFiles')->daily();

Artisan::command('prune:productFiles', function () {
    $productFiles = DB::table('product_files')
        ->where('product_id', null)
        ->where('created_at', '<=', now()->subMonth())
        ->get();
    $publicStorage = Storage::disk('public');
    foreach ($productFiles as $productFile) {
        $publicStorage->delete($productFile->url);
    }
    ProductFile::destroy($productFiles->pluck('id'));
    $this->comment('Unused productFiles removed');
})->purpose('Remove unused productFiles')->daily();

Artisan::command('prune:productAttachments', function () {
    $productAttachments = DB::table('product_attachments')
        ->where('product_id', null)
        ->where('created_at', '<=', now()->subMonth())
        ->get();
    $publicStorage = Storage::disk('public');
    foreach ($productAttachments as $productAttachment) {
        $publicStorage->delete($productAttachment->url);
    }
    ProductAttachment::destroy($productAttachments->pluck('id'));
    $this->comment('Unused productAttachments removed');
})->purpose('Remove unused productAttachments')->daily();

Artisan::command('prune:pageAttachments', function () {
    $pageAttachments = DB::table('page_attachments')
        ->where('page_id', null)
        ->where('created_at', '<=', now()->subMonth())
        ->get();
    $publicStorage = Storage::disk('public');
    foreach ($pageAttachments as $pageAttachment) {
        $publicStorage->delete($pageAttachment->url);
    }
    PageAttachment::destroy($pageAttachments->pluck('id'));
    $this->comment('Unused pageAttachments removed');
})->purpose('Remove unused pageAttachments')->daily();
