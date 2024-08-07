<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCategoriesRequest;
use App\Models\Category;
use App\Models\Filter;
use Illuminate\Support\Facades\DB;

class AdminPanelCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function filters()
    {
        return view('adminPanel.filters', [
            'filters' => Filter::withTrashed()->orderBy('order_priority')->get(),
        ]);
    }

    public function categories()
    {
        return view('adminPanel.categories', [
            'categories' => Category::withTrashed()->orderBy('parent_id')->orderBy('position')->get(),
        ]);
    }

    protected function saveCategory($category, $categories, $index, $parent_id, $parent_remove = false)
    {
        $new = isset($category['new']) && $category['new'];
        $remove = $parent_remove || (isset($category['remove']) && $category['remove']);
        if ($new) {
            $categoryDb = Category::create([
                'parent_id' => $parent_id,
                'name' => $category['name'],
                'slug' => $category['slug'],
                'position' => $index,
            ]);
        } elseif ($remove) {
            $categoryDb = Category::whereId($category['id'])->first();
            $categoryDb->delete();
        } else {
            $restore = isset($category['restore']) && $category['restore'];
            $deletedAt = isset($category['deleted_at']) && $category['deleted_at'];
            if (!$restore && $deletedAt) {
                return;
            }
            $categoryDb = $restore ? Category::onlyTrashed()->whereId($category['id'])->first() : Category::whereId($category['id'])->first();
            $categoryDb->update([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'position' => $index,
                'deleted_at' => null,
            ]);
        }
        if (isset($categories[$category['id']])) {
            foreach ($categories[$category['id']] as $index => $category) {
                $this->saveCategory($category, $categories, $index, $categoryDb->id, $remove);
            }
        }
    }

    public function saveCategories(SaveCategoriesRequest $request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->categories['main-menu'] as $index => $category) {
                $this->saveCategory($category, $request->categories, $index, null);
            }
        });
        return response()->json([
            'categories' => Category::withTrashed()->orderBy('parent_id')->orderBy('position')->get(),
        ]);
    }
}
