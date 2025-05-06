<?php

namespace App\Services;

use App\Models\Setting;

class DeliveryMethodsService
{
    public readonly array $deliveryMethods;

    public function __construct()
    {
        $settDb = Setting::whereIn('name', [
            'INPOST_PRICE',
            'INPOST_ENABLED',
            'COURIER_PRICE',
            'COURIER_ENABLED',
        ])->get(['name', 'value']);
        $sett = $settDb->pluck('value', 'name');
        if ($sett->get('INPOST_ENABLED')) {
            $deliveryMethods['inpost'] = [
                'name' => 'InPost',
                'price' => $sett->get('INPOST_PRICE'),
            ];
        }
        if ($sett->get('COURIER_ENABLED')) {
            $deliveryMethods['courier'] = [
                'name' => __('Courier'),
                'price' => $sett->get('COURIER_PRICE'),
            ];
        }
        $this->deliveryMethods = $deliveryMethods;
    }

    public function getDeliveryPrice(?string $deliveryMethod)
    {
        $deliveryPrice = null;
        if (isset($this->deliveryMethods[$deliveryMethod])) {
            $deliveryPrice = $this->deliveryMethods[$deliveryMethod]['price'];
        }
        return $deliveryPrice;
    }
}
