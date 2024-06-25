<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function category(Request $request, $slug)
    {
        $category = Category::with('products')->where('slug', $slug)->first();
        $activeLinks = '._' . $category->slug;
        $parentIds = [$category->parent_id => $category->parent_id];
        $categories = Category::with(['children' => function (Builder $query) {
            $query->orderBy('position');
        }])->orderBy('position')->get()->keyBy('id');
        while (end($parentIds)) {
            $parent = $categories[end($parentIds)];
            $activeLinks .= ', ._' . $parent->slug;
            $parentIds[$parent->parent_id] = $parent->parent_id;
        }
        return view('category', [
            'parentIds' => $parentIds,
            'activeLinks' => $activeLinks,
            'category' => $category,
            'selectedCategory' => $category,
            'categories' => $categories,
        ]);
    }

    public function index()
    {
        return view('home', [
            'categories' => Category::with(['children' => function (Builder $query) {
                $query->orderBy('position');
            }])->orderBy('position')->get()->keyBy('id'),
        ]);
    }
}
