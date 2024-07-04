<?php

namespace App\Services;

use Traversable;

class CategoryService
{
    public static function categoryOptions(Traversable $categories, $parentId = null, $categoryOptions = [], $prevNameStr = '')
    {
        foreach ($categories as $category) {
            $id = $category->id;
            if ($parentId === $category->parent_id) {
                $patchName = $prevNameStr ? $prevNameStr . ' -> ' . $category->name : $category->name;
                $categoryOptions[] = ['id' => $id, 'patchName' => $patchName];
                $categoryOptions = self::categoryOptions($categories, $id, $categoryOptions, $patchName);
            }
        }
        return $categoryOptions;
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
