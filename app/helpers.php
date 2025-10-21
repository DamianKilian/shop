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
    function price($product, $inTag = true)
    {
        $pieces = explode('.', $product->price);
        if (isset($pieces[1])) {
            if (1 === strlen($pieces[1])) {
                $pieces[1] = $pieces[1] . '0';
            }
        } else {
            $pieces[1] = '00';
        }
        if (!$inTag) {
            return $pieces[0] . ',' . $pieces[1] . 'zł';
        }
        return $pieces[0] . ',<small style="font-size: 1.4rem;">' . ($pieces[1] ?? '00') . 'zł' . '</small>';
    }
}
