<?php

use App\Services\SettingService;

if (! function_exists('sett')) {
    function sett($settingName)
    {
        $settingService = app()->make(SettingService::class);
        return $settingService->settings[$settingName];
    }
}
