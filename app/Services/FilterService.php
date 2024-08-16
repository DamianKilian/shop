<?php

namespace App\Services;

use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

class FilterService
{
    public static function getFiltersQuery(Request $request, $categoryChildrenIds = [])
    {
        return Filter::when($categoryChildrenIds, function ($query, $categoryChildrenIds) {
            return $query->whereHas('categories', function (Builder $query) use ($categoryChildrenIds) {
                $query->whereIn('categories.id', $categoryChildrenIds);
            });
        })->orderByDesc('id');
    }

    public static function getFilters(Request $request, $categoryChildrenIds = [], $paginate = 20)
    {
        $filters = self::getFiltersQuery($request, $categoryChildrenIds)->paginate($paginate);
        return $filters;
    }
}
