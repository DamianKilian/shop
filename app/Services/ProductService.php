<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public static function getProductsFilters(Request $request)
    {
        $pf = [];
        $pf['searchValue'] = $request->searchValue;
        return $pf;
    }

    // public static function productsFiltersNum($pf)
    // {
    //     $productsFiltersNum = 0;
    //     foreach ($pf as $value) {
    //         if ($value) {
    //             $productsFiltersNum += 1;
    //         }
    //     }
    //     return $productsFiltersNum;
    // }

    public static function searchFilters(Request $request, $categoryChildrenIds = [], $withCategory = false, $paginate = 20, $withDesc = true)
    {
        $pf = self::getProductsFilters($request);
        $products = Product::with(['productPhotos' => function (Builder $query) {
            $query->orderBy('position');
        }])->when($pf['searchValue'], function ($query, $searchValue) {
            return $query->whereFullText(['title', 'description_str'], $searchValue);
        })->when($categoryChildrenIds, function ($query, $categoryChildrenIds) {
            return $query->whereIn('category_id', $categoryChildrenIds);
        })->when($withCategory, function ($query) {
            return $query->with('category');
        })->paginate($paginate);
        foreach ($products as &$product) {
            if (0 === $product->productPhotos->count()) {
                continue;
            }
            foreach ($product->productPhotos as &$photo) {
                $photo->fullUrlSmall = Storage::url($photo->url_small);
            }
            unset($photo);
        }
        unset($product);
        if ($withDesc) {
            foreach ($products as &$product) {
                $product->descStr = ProductService::limitProductDescStr($product->description_str);
            }
            unset($product);
        }
        return $products;
    }

    public static function getProductDescStr($description)
    {
        $descStr = '';
        foreach (json_decode($description, true)['blocks'] as $block) {
            $data = $block['data'];
            if (isset($data['text'])) {
                $descStr .= $data['text'] . ' ';
            } elseif (isset($data['items'])) {
                foreach ($data['items'] as $itemText) {
                    $descStr .= $itemText . ' ';
                }
            }
        }
        $descStr = preg_replace('/\s\s+/', ' ', $descStr);
        $descStr = preg_replace('/\<br\>\s$/', '', $descStr);
        return $descStr;
    }

    public static function limitProductDescStr($descStr, $limit = 200, $suffix = ' ( ... )')
    {
        return Str::limit(strip_tags($descStr), $limit, $suffix);
    }
}
