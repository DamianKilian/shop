<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SettingCategory;
use App\Models\SettingValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SettingService
{
    public readonly Collection $settings;

    public function __construct()
    {
        $settingsDb = Setting::all('name', 'value');
        $this->settings = $settingsDb->pluck('value', 'name');
    }

    public static function createSettingCategory($name, $orderPriority = null)
    {
        if (!$orderPriority) {
            $orderPriority = SettingCategory::max('order_priority') + 1000;
        }
        $category = SettingCategory::create([
            'name' => $name,
            'order_priority' => $orderPriority,
        ]);
        return $category;
    }

    public static function createSetting($category, $name, $desc, $inputType, $defaultValue, $orderPriority = null)
    {
        if (is_string($category)) {
            $category =  SettingCategory::where('name', $category)->first();
        }
        if (!$orderPriority) {
            $orderPriority = Setting::where('setting_category_id', $category->id)->max('order_priority') + 1000;
        }
        $setting = Setting::create([
            'name' => $name,
            'desc' => $desc,
            'input_type' => $inputType,
            'value' => $defaultValue,
            'default_value' => $defaultValue,
            'order_priority' => $orderPriority,
            'setting_category_id' => $category->id,
        ]);
        return $setting;
    }

    public static function attachSettingValue($setting, $settingValueName, $value, $orderPriority = null)
    {
        if (is_string($setting)) {
            $setting =  Setting::where('name', $setting)->with('settingValues')->get();
        }
        if (!$orderPriority) {
            $settingValues = $setting->settingValues();
            $orderPriority = $settingValues->max('order_priority') + 1000;
        }
        $settingValue = SettingValue::create([
            'name' => $settingValueName,
            'value' => $value,
            'order_priority' => $orderPriority,
        ]);
        $setting->settingValues()->attach($settingValue->id);
    }

    public static function addSettings($kind)
    {
        $msg = '';
        DB::transaction(function () use ($kind, &$msg) {
            if ('seo' === $kind) {
                $msg = self::addSeoSettings($kind);
            }
        });
        return $msg;
    }

    public static function addSeoSettings($kind)
    {
        $name = 'SEO settings';
        $category = SettingCategory::where('name', $name)->first();
        if($category){
            return 'settings already exist';
        }
        $category = self::createSettingCategory($name, 500);
        self::createSetting(
            $category,
            'TITLE_MAIN',
            'title of main page, {shopName}',
            'textarea',
            '{shopName}, niskie ceny, strona główna',
            1000
        );
        self::createSetting(
            $category,
            'TITLE_CATEGORY',
            'title of category, {cat}, {parentCat}, {parentParentCat}, {shopName}',
            'textarea',
            '{shopName}, kup w {cat}, {parentCat}, {parentParentCat}, niskie ceny',
            2000
        );
        self::createSetting(
            $category,
            'TITLE_PRODUCT',
            'title of products, {product}, {cat}, {parentCat}, {price}, {shopName}',
            'textarea',
            '{shopName}, kup {product} w {cat}, {parentCat}, niskie ceny',
            3000
        );

        self::createSetting(
            $category,
            'DESC_MAIN',
            'desc of main page, {shopName}',
            'textarea',
            '{shopName}, niskie ceny',
            1000
        );
        self::createSetting(
            $category,
            'DESC_CATEGORY',
            'desc of category, {cat}, {parentCat}, {parentParentCat}, {shopName}',
            'textarea',
            '{shopName}, kup w {cat}, {parentCat}, {parentParentCat}, niskie ceny',
            2000
        );
        self::createSetting(
            $category,
            'DESC_PRODUCT',
            'desc of products, {product}, {cat}, {parentCat}, {price}, {shopName}',
            'textarea',
            '{shopName}, kup {product} za {price} w {cat}, {parentCat}, niskie ceny',
            3000
        );
        return $kind . ' settings was added!';
    }
}
