<?php

use App\Services\AppService;
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

Artisan::command('prune:files', function () {
    AppService::pruneFiles('files');
    $this->comment('Unused files removed');
})->purpose('Remove unused pageFiles')->daily();

Artisan::command('prune:attachments', function () {
    AppService::pruneFiles('attachments');
    $this->comment('Unused pageAttachments removed');
})->purpose('Remove unused pageAttachments')->daily();

Artisan::command('logs:send', function () {
    AppService::logsSend();
    $this->comment('Logs have been sent to: "' . env('LOG_SEND_EMAILS') . '"');
})->purpose('Send logs to email')->hourly();
