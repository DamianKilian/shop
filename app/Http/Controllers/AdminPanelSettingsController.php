<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SettingCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminPanelSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings()
    {
        return view('adminPanel.settings', []);
    }

    public function getSettings()
    {
        $settingCategories = SettingCategory::with(['settings' => function (Builder $query) {
            $query->with([
                'settingValues' => function (Builder $query) {
                    $query->orderBy('order_priority');
                }
            ])->orderBy('order_priority');
        }])->orderBy('order_priority')->get();
        return response()->json([
            'settingCategories' => $settingCategories,
        ]);
    }

    public function restoreSettings(Request $request)
    {
        $settingIds = [];
        foreach ($request->settings as $setting) {
            $settingIds[] = $setting['id'];
        }
        $settingsDb = Setting::whereIn('id', $settingIds)->get();
        foreach ($settingsDb as $sDb) {
            $sDb->value = $sDb->default_value;
            $sDb->save();
        }
    }

    public function saveSetting(Request $request)
    {
        $setting = Setting::where('id', $request->settingId)->first();
        $deliveryPriceSettingNames = [
            'INPOST_PRICE',
            'COURIER_PRICE',
        ];
        $v = trim($request->value, '0');
        if (false !== array_search($setting->name, $deliveryPriceSettingNames)) {
            $pattern = '/[\,]/';
            $v = preg_replace($pattern, '.', $v);
            $pattern2 = '/[^0-9.]/';
            $v = preg_replace($pattern2, '', $v);
            $v .= '00';
            $vArr = explode('.', $v);
            if (isset($vArr[1])) {
                $decimal = substr(array_pop($vArr), 0, 2);
            }
            $v = implode('', $vArr) . '.' . $decimal;
        }
        $setting->update(['value' => $v]);
    }
}
