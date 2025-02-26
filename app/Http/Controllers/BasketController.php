<?php

namespace App\Http\Controllers;

use App\Services\BasketService;
use App\Services\DeliveryMethodsService;
use Illuminate\Http\Request;

class BasketController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function basketIndex(DeliveryMethodsService $deliveryMethodsService)
    {
        return view('basket.index', [
            'deliveryMethods' => json_encode($deliveryMethodsService->deliveryMethods),
        ]);
    }

    public function getProductsInBasketData(Request $request)
    {
        $productsInBasketData = BasketService::getProductsInBasketData($request->productsInBasket);
        BasketService::productsInBasketDataForList($productsInBasketData);
        return response()->json([
            'productsInBasketData' => $productsInBasketData,
        ]);
    }

    public function getBasketSummary(Request $request, DeliveryMethodsService $deliveryMethodsService)
    {
        $summary = BasketService::getBasketSummary($request->productsInBasket, $request->deliveryMethod, $deliveryMethodsService);
        return response()->json([
            'basketLastChange' => $request->basketLastChange,
            'summary' => $summary['formatted'],
        ]);
    }
}
