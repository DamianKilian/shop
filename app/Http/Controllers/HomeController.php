<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use App\Services\ProductService;
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
        $categories = CategoryService::getCategories();
        $category = Category::where('slug', $slug)->first();
        $categoryChildrenIds = CategoryService::getCategoryChildrenIds([$category->id], $categories);
        $products = ProductService::searchFilters($request, $categoryChildrenIds);
        unset($product);
        $activeLinks = '._' . $category->slug;
        $parentIds = [$category->parent_id => $category->parent_id];
        while (end($parentIds)) {
            $parent = $categories[end($parentIds)];
            $activeLinks .= ', ._' . $parent->slug;
            $parentIds[$parent->parent_id] = $parent->parent_id;
        }
        return view('category', [
            'categoryChildrenIds' => json_encode($categoryChildrenIds),
            'products' => $products,
            'parentIds' => $parentIds,
            'activeLinks' => $activeLinks,
            'category' => $category,
            'selectedCategory' => $category,
            'categories' => $categories,
        ]);
    }

    public function getProductsView(Request $request)
    {
        $categoryChildrenIds = json_decode($request->categoryChildrenIds);
        $products = ProductService::searchFilters($request, $categoryChildrenIds);
        return view('_partials.products', [
            'products' => $products
        ]);
    }

    public function index()
    {
        return view('home', [
            'categories' => CategoryService::getCategories(),
        ]);
    }
}
