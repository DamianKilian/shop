<?php

use App\Models\PageFile;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();




// Schedules:

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
