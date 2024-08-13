<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCategoriesRequest;
use App\Models\Category;
use App\Models\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AdminPanelCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function categories()
    {
        return view('adminPanel.categories', [
            'filters' => Filter::orderBy('order_priority')->get(['id', 'name', 'order_priority']),
            'categories' => $this->categoriesWithFilters(),
        ]);
    }

    protected function categoriesWithFilters()
    {
        return Category::withTrashed()->with(['filters' => function (Builder $query) {
            $query->select('filters.id', 'filters.name', 'filters.order_priority');
        }])->orderBy('parent_id')->orderBy('position')->get();
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
        if (isset($category['filtersById'])) {
            $categoryDb->filters()->sync(array_keys($category['filtersById']));
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
            'categories' => $this->categoriesWithFilters(),
        ]);
    }
}
