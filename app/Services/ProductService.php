<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public static function searchFilters(Request $request, $categoryChildrenIds = [], $withCategory = true, $paginate = 20)
    {
        $products = Product::with(['productPhotos' => function (Builder $query) {
            $query->orderBy('position');
        }])->when($categoryChildrenIds, function ($query, $categoryChildrenIds) {
            return $query->whereIn('category_id', $categoryChildrenIds);
        })->when($request->searchValue, function ($query, $searchValue) {
            return $query->whereFullText(['title', 'description'], $searchValue);
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
        return $products;
    }

    public static function getProductDescStr($product)
    {
        $descStr = '';
        foreach (json_decode($product->description, true)['blocks'] as $block) {
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
}
