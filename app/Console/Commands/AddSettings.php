<?php

namespace App\Console\Commands;

use App\Services\SettingService;
use Illuminate\Console\Command;

class AddSettings extends Command
{
    protected $signature = 'app:add-settings {kind}';

    protected $description = 'Add settings';

    public function handle()
    {
        $kind = $this->argument('kind');
        $msg = SettingService::addSettings($kind);
        $this->info($msg);
    }
}
