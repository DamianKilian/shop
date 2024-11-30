<?php

use App\Models\Setting;
use App\Models\SettingCategory;
use App\Models\SettingValue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SettingValue::truncate();
        Setting::truncate();
        SettingCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $mainSettings = SettingCategory::create([
            'name' => __('Main settings'),
            'order_priority' => 1000,
        ]);
        $pageEditorSettings = SettingCategory::create([
            'name' => __('Page editor settings'),
            'order_priority' => 2000,
        ]);

        $productOpenTarget = Setting::create([
            'name' => 'PRODUCT_OPEN_TARGET',
            'desc' => __('Product page opening mechanism'),
            'input_type' => 'select',
            'value' => '_blank',
            'default_value' => '_blank',
            'order_priority' => 1000,
            'setting_category_id' => $mainSettings->id,
        ]);
        SettingValue::create([
            'name' => __('Open in a new window or tab'),
            'value' => '_blank',
            'order_priority' => 1000,
            'setting_id' => $productOpenTarget->id,
        ]);
        SettingValue::create([
            'name' => __('Open in a same window or tab'),
            'value' => '_self',
            'order_priority' => 2000,
            'setting_id' => $productOpenTarget->id,
        ]);

        Setting::create([
            'name' => 'THUMBNAIL_CONTAIN_SIZE',
            'desc' => __('Max. size of thumbnail'),
            'input_type' => 'text',
            'value' => '350',
            'default_value' => '350',
            'order_priority' => 2000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        Setting::create([
            'name' => 'THUMBNAIL_GALLERY_SIZE_H',
            'desc' => __('Thumbnail  size in gallery - height'),
            'input_type' => 'text',
            'value' => '200',
            'default_value' => '200',
            'order_priority' => 3000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        Setting::create([
            'name' => 'THUMBNAIL_GALLERY_SIZE_W',
            'desc' => __('Thumbnail  size in gallery - width'),
            'input_type' => 'text',
            'value' => '200',
            'default_value' => '200',
            'order_priority' => 4000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
    }

    public function down(): void {}
};
