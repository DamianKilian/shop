<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public static function getProductsFilters(Request $request)
    {
        $pf = [];
        $pf['searchValue'] = $request->searchValue;
        $pf['maxPrice'] = $request->maxPrice;
        $pf['minPrice'] = $request->minPrice;
        $pf['filterOptions'] = $request->filterOptions;
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


    public static function getMaxProductsPrice($categoryChildrenIds = [])
    {
        return Cache::remember('maxProductsPrice' . implode($categoryChildrenIds), 60, function () use ($categoryChildrenIds) {
            return Product::when($categoryChildrenIds, function ($query, $categoryChildrenIds) {
                return $query->whereIn('category_id', $categoryChildrenIds);
            })->max('price');
        });
    }

    public static function searchFiltersQuery(Request $request, $categoryChildrenIds = [], $withCategory = false, $onlyActive = true)
    {
        $pf = self::getProductsFilters($request);
        return Product::select(['id', 'title', 'description_str', 'active', 'price', 'quantity', 'slug', 'category_id'])
            ->where('slug', '!=', config('my.preview_slug'))
            ->with(['productImages' => function (Builder $query) {
                $query->whereDisplayType('productPhotosGallery')->orderBy('position');
            }])->when($pf['searchValue'], function ($query, $searchValue) {
                return $query->whereFullText(['title', 'description_str'], $searchValue);
            })->when($pf['maxPrice'], function ($query, $maxPrice) {
                return $query->where('price', '<=', $maxPrice);
            })->when($pf['minPrice'], function ($query, $minPrice) {
                return $query->where('price', '>=', $minPrice);
            })->when($pf['filterOptions'], function ($query, $filterOptions) {
                $filterOptions = array_map('intval', explode("|", $filterOptions));
                return $query->whereHas('filterOptions', function (Builder $query) use ($filterOptions) {
                    $query->whereIn('filter_options.id', $filterOptions);
                });
            })->when($categoryChildrenIds, function ($query, $categoryChildrenIds) {
                return $query->whereIn('category_id', $categoryChildrenIds);
            })->when($withCategory, function ($query) {
                return $query->with('category');
            })->when($onlyActive, function ($query) {
                return $query->whereActive(true);
            })->when($request->sortValue, function ($query, $sortValue) {
                switch ($sortValue) {
                    case 'price_desc':
                        return $query->orderBy('price', 'desc');
                        break;
                    case 'price_asc':
                        return $query->orderBy('price', 'asc');
                        break;
                }
            })->orderByDesc('id');
    }

    public static function searchFilters(Request $request, $categoryChildrenIds = [], $withCategory = false, $paginate = 20, $withDesc = true, $onlyActive = true)
    {
        $products = self::searchFiltersQuery($request, $categoryChildrenIds, $withCategory, $onlyActive)->paginate($paginate);
        foreach ($products as &$product) {
            if (0 === $product->productImages->count()) {
                continue;
            }
            foreach ($product->productImages as &$photo) {
                $photo->fullUrlSmall = Storage::url($photo->url_thumbnail);
            }
            unset($photo);
        }
        unset($product);
        if ($withDesc) {
            foreach ($products as &$product) {
                if (is_array($withDesc)) {
                    $product->descStr = ProductService::limitProductDescStr($product->description_str, $withDesc);
                } else {
                    $product->descStr = ProductService::limitProductDescStr($product->description_str);
                }
            }
            unset($product);
        }
        return $products;
    }

    public static function getProductDescStr($description)
    {
        if (!json_decode($description, true)) {
            return $description;
        }
        $descStr = '';
        foreach (json_decode($description, true)['blocks'] as $block) {
            if (false === array_search($block['type'], ['header', 'paragraph', 'list'])) {
                continue;
            }
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

    public static function limitProductDescStr($descStr, $params = [])
    {
        $limit = isset($params['limit']) ? $params['limit'] : 200;
        $suffix = isset($params['suffix']) ? $params['suffix'] : ' ( ... )';
        return Str::limit(strip_tags($descStr), $limit, $suffix);
    }
}
