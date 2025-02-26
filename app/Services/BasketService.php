<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class BasketService
{
    public static function getProductsInBasketData($productsInBasket, $withProductImages = true,  $cols = ['id', 'title', 'slug', 'price'])
    {
        return Product::whereIn('id', array_keys($productsInBasket))->when($withProductImages, function ($query) {
            $query->with(['productImages' => function ($query) {
                $query->select('url_thumbnail', 'product_id')->whereDisplayType('productPhotosGallery')->orderBy('position')->take(1);
            }]);
        })->get($cols)->keyBy('id');
    }

    public static function productsInBasketDataForList(&$productsInBasketData)
    {
        foreach ($productsInBasketData as &$product) {
            $product->url = route('product', [$product->slug]);
            if ($product->productImages->isNotEmpty()) {
                $photo = $product->productImages->first();
                $product->fullUrlSmall = Storage::url($photo->url_thumbnail);
            } else {
                $product->fullUrlSmall = '';
            }
            $product->unsetRelation('productImages');
        };
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
        $raw = $summary;
        foreach ($summary as &$value) {
            $value = number_format($value, 2, ',', ' ');
        }
        return [
            'formatted' => $summary,
            'raw' => $raw,
        ];
    }
}
