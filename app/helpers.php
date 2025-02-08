<?php

use App\Services\SettingService;

if (! function_exists('sett')) {
    function sett($settingName)
    {
        $settingService = app()->make(SettingService::class);
        return $settingService->settings[$settingName];
    }
}

if (! function_exists('price')) {
    function price($product)
    {
        $pieces = explode('.', $product->price);
        return $pieces[0] . ',<small style="font-size: 1.4rem;">' . $pieces[1] . 'zł' . '</small>';
    }
}
