<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BasketController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function basketIndex()
    {
        return view('basket.index', []);
    }

    public function getProductsInBasketData(Request $request)
    {
        $productsInBasketData = Product::whereIn('id', array_keys($request->productsInBasket))->with(['productImages' => function ($query) {
            $query->select('url_thumbnail', 'product_id')->whereDisplayType('productPhotosGallery')->orderBy('position')->first();
        }])->get(['id', 'title', 'slug'])->keyBy('id');
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
        return response()->json([
            'productsInBasketData' => $productsInBasketData,
        ]);
    }
}
