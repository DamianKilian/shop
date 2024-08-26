<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Filter;
use Traversable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    public static function getCategories()
    {
        return Cache::remember('categories', 60, function () {
            return Category::with(['children' => function (Builder $query) {
                $query->orderBy('position');
            }])->orderBy('position')->get()->keyBy('id');
        });
    }

    public static function categoryOptions(Traversable $categories, $parentId = null, $categoryOptions = [], $prevNameStr = '')
    {
        foreach ($categories as $category) {
            if ($parentId === $category->parent_id) {
                $id = $category->id;
                $patchName = $prevNameStr ? $prevNameStr . ' -> ' . $category->name : $category->name;
                $categoryOptions[] = ['id' => $id, 'patchName' => $patchName];
                $categoryOptions = self::categoryOptions($categories, $id, $categoryOptions, $patchName);
            }
        }
        return $categoryOptions;
    }

    public static function getCategoryChildrenIds($categoryChildrenIds, Traversable $categories = null)
    {
        if (!$categories) {
            $categories = Category::with(['children' => function (Builder $query) {
                $query->orderBy('position');
            }])->orderBy('position')->get();
        }
        $categoryId = end($categoryChildrenIds);
        foreach ($categories as $category) {
            if ($categoryId === $category->parent_id) {
                $categoryChildrenIds[] = $category->id;
                $categoryChildrenIds = self::getCategoryChildrenIds($categoryChildrenIds, $categories);
            }
        }
        return $categoryChildrenIds;
    }

    public static function getParentCategories($categoryId, $categories)
    {
        $parentCategories = [$categoryId => $categories[$categoryId]];
        while (end($parentCategories)->parent_id) {
            $parent = $categories[end($parentCategories)->parent_id];
            $parentCategories[$parent->id] = $parent;
        }
        return array_reverse($parentCategories, true);
    }

    public static function getCategoryFilters($parentCategoriesIds)
    {
        return Cache::remember('categoryFilters' . implode($parentCategoriesIds), 60, function () use ($parentCategoriesIds) {
            return Filter::whereHas('categories', function (Builder $query) use ($parentCategoriesIds) {
                $query->whereIn('categories.id', $parentCategoriesIds);
            })
                ->with(['filterOptions' => fn($query) => $query->orderBy('order_priority')])
                ->orderBy('order_priority')->get();
        });
    }

    // public static function categoriesIdTree(Traversable $categories, $parentId = null, $categoriesIdTree = [])
    // {
    //     foreach ($categories as $category) {
    //         $id = $category->id;
    //         if ($parentId === $category->parent_id) {
    //             $categoriesIdTree[$id] = [];
    //             $categoriesIdTree[$id] = self::categoriesIdTree($categories, $id, $categoriesIdTree[$id]);
    //         }
    //     }
    //     return $categoriesIdTree;
    // }
}
