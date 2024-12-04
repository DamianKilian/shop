<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Setting;
use App\Models\SettingCategory;
use App\Models\SettingValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin-panel/settings');

        $response->assertStatus(200);
    }

    public function test_getSettings(): void
    {
        $user = User::factory()->create();
        SettingCategory::truncate();
        Setting::truncate();
        SettingValue::truncate();
        $settingCategory = SettingCategory::factory()->create([
            'name' => 'settingCategoryName',
            'order_priority' => 2000,
        ]);
        $setting = Setting::factory()->create([
            'name' => 'settingName',
            'input_type' => 'select',
            'setting_category_id' => $settingCategory->id,
        ]);
        $s1 = SettingValue::factory()->create([
            'name' => 'settingValueName',
            'value' => '_blank',
            'order_priority' => 1000,
        ]);
        $s2 = SettingValue::factory()->create([
            'name' => 'settingValueName2',
            'value' => '_self',
            'order_priority' => 2000,
        ]);
        $setting->settingValues()->attach([$s1->id, $s2->id]);
        $settingCategory2 = SettingCategory::factory()->create([
            'name' => 'settingCategoryName2',
            'order_priority' => 1000,
        ]);
        Setting::factory()->create([
            'name' => 'settingName2',
            'setting_category_id' => $settingCategory2->id,
        ]);

        $response = $this->actingAs($user)->postJson('/admin-panel/get-settings', []);

        $response->assertStatus(200);
        assertTrue('settingCategoryName' === $response['settingCategories'][1]['name']);
        assertTrue('settingName' === $response['settingCategories'][1]['settings'][0]['name']);
        assertTrue('settingValueName2' === $response['settingCategories'][1]['settings'][0]['setting_values'][1]['name']);
    }

    public function test_restoreSettings(): void
    {
        $user = User::factory()->create();
        SettingCategory::truncate();
        Setting::truncate();
        SettingValue::truncate();
        $settingCategory = SettingCategory::factory()->create([
            'name' => 'settingCategoryName2',
            'order_priority' => 1000,
        ]);
        $setting = Setting::factory()->create([
            'name' => 'settingName',
            'value' => 'value',
            'default_value' => 'default_value',
            'setting_category_id' => $settingCategory->id,
        ]);
        $setting2 = Setting::factory()->create([
            'name' => 'settingName2',
            'value' => 'value2',
            'default_value' => 'default_value2',
            'setting_category_id' => $settingCategory->id,
        ]);

        $response = $this->actingAs($user)->postJson('/admin-panel/restore-settings', [
            'settings' => [
                ['id' => $setting->id,],
                ['id' => $setting2->id,],
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'value' => 'default_value',
        ]);
        $this->assertDatabaseHas('settings', [
            'id' => $setting2->id,
            'value' => 'default_value2',
        ]);
    }

    public function test_saveSetting(): void
    {
        $user = User::factory()->create();
        SettingCategory::truncate();
        Setting::truncate();
        SettingValue::truncate();
        $settingCategory = SettingCategory::factory()->create([
            'name' => 'settingCategoryName2',
            'order_priority' => 1000,
        ]);
        $setting = Setting::factory()->create([
            'name' => 'settingName',
            'value' => 'value',
            'default_value' => 'default_value',
            'setting_category_id' => $settingCategory->id,
        ]);

        $response = $this->actingAs($user)->postJson('/admin-panel/save-setting', [
            'settingId' => $setting->id,
            'value' => 'newValue',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'value' => 'newValue',
        ]);
    }
}
