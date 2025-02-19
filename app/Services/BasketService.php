<?php

namespace App\Services;

use App\Models\Product;

class BasketService
{
    public static function getProductsInBasketData($productsInBasket, $withProductImages = true,  $cols = ['id', 'title', 'slug', 'price'])
    {
        return Product::whereIn('id', array_keys($productsInBasket))->when($withProductImages, function ($query) {
            $query->with(['productImages' => function ($query) {
                $query->select('url_thumbnail', 'product_id')->whereDisplayType('productPhotosGallery')->orderBy('position')->first();
            }]);
        })->get($cols)->keyBy('id');
    }

    public static function getBasketSummary($productsInBasket, $deliveryMethod, DeliveryMethodsService $deliveryMethodsService)
    {
        $summary = [
            'productsPrice' => 0,
            'deliveryPrice' => 0,
            'totalPrice' => 0,
        ];
        $productsInBasketData = self::getProductsInBasketData($productsInBasket, false, ['id', 'price']);
        foreach ($productsInBasketData as $product) {
            $summary['productsPrice'] += $product->price * $productsInBasket[$product->id]['num'];
        };
        $summary['deliveryPrice'] = $deliveryMethodsService->getDeliveryPrice($deliveryMethod);
        $summary['totalPrice'] = $summary['deliveryPrice'] + $summary['productsPrice'];
        foreach ($summary as &$value) {
            $value = number_format($value, 2, ',', ' ');
        }
        return $summary;
    }
}
