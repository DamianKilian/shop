<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Schedules:

Schedule::call(function () {
    DB::table('suggestions')
        ->where('last_used', '<=', now()->subYear())
        ->delete();
})->daily();

Schedule::call(function () {
    $pageFiles = DB::table('page_files')
        ->where('page_id', null)
        ->where('created_at', '<=', now()->subMonth())
        ->get();
    $publicStorage = Storage::disk('public');
    foreach ($pageFiles as $pageFile) {
        $publicStorage->delete($pageFile->url);
    }
    $pageFiles->delete();
})->daily();
