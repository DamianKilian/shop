<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductService
{
    public static function searchFilters(Request $request, $withCategory = true, $paginate = 20)
    {
        $products = Product::with(['productPhotos' => function (Builder $query) {
            $query->orderBy('position');
        }])->when($request->category, function ($query, $category) {
            return $query->where('category_id', $category['id']);
        })->when($request->searchValue, function ($query, $searchValue) {
            return $query->whereFullText(['title', 'description'], $searchValue);
        })->when($withCategory, function ($query) {
            return $query->with('category');
        })->paginate($paginate);
        return $products;
    }
}
