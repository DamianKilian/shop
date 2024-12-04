<?php

use App\Services\AppService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('app:add-settings', [
            '--isSqlite' => AppService::isSqlite($this),
        ]);
    }

    public function down(): void {}
};
