<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\SettingCategory;
use App\Models\SettingValue;
use App\Services\SettingService;
use Illuminate\Support\Facades\DB;

class ResetSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-settings {--isSqlite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(SettingService $settingService)
    {
        if (!$this->option('isSqlite')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            SettingValue::truncate();
            Setting::truncate();
            SettingCategory::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $mainSettings = SettingCategory::create([
            'name' => 'Main settings',
            'order_priority' => 1000,
        ]);
        $pageEditorSettings = SettingCategory::create([
            'name' => 'Page editor settings',
            'order_priority' => 3000,
        ]);

        $productOpenTarget = Setting::create([
            'name' => 'PRODUCT_OPEN_TARGET',
            'desc' => 'Product page opening mechanism',
            'input_type' => 'select',
            'value' => '_blank',
            'default_value' => '_blank',
            'order_priority' => 1000,
            'setting_category_id' => $mainSettings->id,
        ]);
        $new = SettingValue::create([
            'name' => 'Open in a new window or tab',
            'value' => '_blank',
            'order_priority' => 1000,
        ]);
        $same = SettingValue::create([
            'name' => 'Open in a same window or tab',
            'value' => '_self',
            'order_priority' => 2000,
        ]);
        $productOpenTarget->settingValues()->attach([$new->id, $same->id]);

        Setting::create([
            'name' => 'THUMBNAIL_MAX_SIZE',
            'desc' => 'Max. size of thumbnail',
            'input_type' => 'text',
            'value' => '350',
            'default_value' => '350',
            'order_priority' => 2000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        Setting::create([
            'name' => 'GALLERY_COLUMN_WIDTH',
            'desc' => 'Column width in gallery in pixels',
            'input_type' => 'text',
            'value' => '200',
            'default_value' => '200',
            'order_priority' => 3000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        Setting::create([
            'name' => 'GALLERY_COLUMN_GAP',
            'desc' => 'Gallery column gap in pixels',
            'input_type' => 'text',
            'value' => '5',
            'default_value' => '5',
            'order_priority' => 4000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        Setting::create([
            'name' => 'GALLERY_ROW_GAP',
            'desc' => 'Gallery row gap in pixels',
            'input_type' => 'text',
            'value' => '5',
            'default_value' => '5',
            'order_priority' => 5000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        $GalleryImageFit = Setting::create([
            'name' => 'GALLERY_IMG_FIT',
            'desc' => 'Gallery image fit',
            'input_type' => 'select',
            'value' => 'contain',
            'default_value' => 'contain',
            'order_priority' => 6000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        $GalleryImageFitMasonry = Setting::create([
            'name' => 'GALLERY_IMG_FIT_MASONRY',
            'desc' => 'Masonry gallery image fit',
            'input_type' => 'select',
            'value' => 'contain',
            'default_value' => 'contain',
            'order_priority' => 7000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        $CarouselImageFit = Setting::create([
            'name' => 'CAROUSEL_IMG_FIT',
            'desc' => 'Carousel image fit',
            'input_type' => 'select',
            'value' => 'contain',
            'default_value' => 'contain',
            'order_priority' => 8000,
            'setting_category_id' => $pageEditorSettings->id,
        ]);
        $fill = SettingValue::create([
            'name' => 'fill',
            'value' => 'fill',
            'order_priority' => 1000,
        ]);
        $contain = SettingValue::create([
            'name' => 'contain',
            'value' => 'contain',
            'order_priority' => 2000,
        ]);
        $cover = SettingValue::create([
            'name' => 'cover',
            'value' => 'cover',
            'order_priority' => 3000,
        ]);
        $none = SettingValue::create([
            'name' => 'none',
            'value' => 'none',
            'order_priority' => 4000,
        ]);
        $scaleDown = SettingValue::create([
            'name' => 'scale-down',
            'value' => 'scale-down',
            'order_priority' => 5000,
        ]);
        $GalleryImageFit->settingValues()->attach([$fill->id, $contain->id, $cover->id, $none->id, $scaleDown->id]);
        $GalleryImageFitMasonry->settingValues()->attach([$fill->id, $contain->id, $cover->id, $none->id, $scaleDown->id]);
        $CarouselImageFit->settingValues()->attach([$fill->id, $contain->id, $cover->id, $none->id, $scaleDown->id]);

        $settingService->addSettings('seo');
    }
}
