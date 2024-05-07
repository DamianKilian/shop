<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCategoriesRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdminPanelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function products()
    {
        return view('adminPanel.products');
    }

    public function categories()
    {
        return view('adminPanel.categories', [
            'categories' => Category::orderBy('parent_id')->orderBy('position')->get(),

        ]);
    }

    protected function saveCategory($category, $categories, $index, $parent_id)
    {
        if (isset($category['removed']) && $category['removed']) {
            Category::whereId($category['id'])->forceDelete();
            return;
        }
        $categoryPrepared = [
            'parent_id' => $parent_id,
            'name' => $category['name'],
            'position' => $index,
            'new' => isset($category['new']) && $category['new'],
        ];
        if ($categoryPrepared['new']) {
            $categoryDb = Category::create($categoryPrepared);
        } else {
            $categoryDb = Category::whereId($category['id'])->first();
            $categoryDb->update([
                'name' => $categoryPrepared['name'],
                'position' => $categoryPrepared['position'],
            ]);
        }
        if (isset($categories[$category['id']])) {
            foreach ($categories[$category['id']] as $index => $category) {
                $this->saveCategory($category, $categories, $index, $categoryDb->id);
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
            'categories' => Category::orderBy('parent_id')->orderBy('position')->get(),
        ]);
    }
}
