<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Schedules:

Schedule::call(function () {
    DB::table('suggestions')
        ->where('last_used', '<=', now()->subYear())
        ->delete();
})->daily();
