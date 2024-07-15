<?php

namespace App\Services;

use App\Models\Category;
use Traversable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CategoryService
{
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
