<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;

class SettingService
{
    public readonly Collection $settings;

    public function __construct()
    {
        $settingsDb = Setting::all('name', 'value');
        $this->settings = $settingsDb->pluck('value', 'name');
    }
}
