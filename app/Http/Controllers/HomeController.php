<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetProductsViewRequest;
use App\Models\Category;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $parentCategoriesIds = array_keys(CategoryService::getParentCategories($category->id, $categories));
        $filters = CategoryService::getCategoryFilters($parentCategoriesIds);
        return view('category', [
            'filters' => $filters,
            'maxProductsPrice' => ProductService::getMaxProductsPrice($categoryChildrenIds),
            'categoryChildrenIds' => json_encode($categoryChildrenIds),
            'products' => $products,
            'parentIds' => $parentIds,
            'activeLinks' => $activeLinks,
            'category' => $category,
            'selectedCategory' => $category,
            'categories' => $categories,
        ]);
    }

    public function getProductsView(GetProductsViewRequest $request)
    {
        $categoryChildrenIds = json_decode($request->categoryChildrenIds);
        $products = ProductService::searchFilters($request, $categoryChildrenIds);
        return view('_partials.products', [
            'products' => $products
        ]);
    }

    public function getProductsViewAllCategories(GetProductsViewRequest $request)
    {
        $products = ProductService::searchFilters($request);
        $categories = CategoryService::getCategories();
        foreach ($products as $product) {
            $product->categories = CategoryService::getParentCategories($product->category_id, $categories);
        }
        return view('_partials.products', [
            'products' => $products
        ]);
    }

    public function getProductNums(Request $request)
    {
        $productNums = [];
        $filterProductIds = ProductService::searchFiltersQuery($request)->pluck('id');
        $c = $filterProductIds->count();
        if ($c > 0 && $c < 500) {
            $productNums = DB::table('products')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->select(DB::raw('count(products.id) as product_num'), 'categories.slug',)
                ->whereIn('products.id', $filterProductIds)
                ->groupBy('categories.slug')
                ->get();
        }
        return response()->json([
            'productNums' => $productNums,
        ]);
    }

    public function index(Request $request)
    {
        $products = null;
        if ($request->searchValue) {
            $products = ProductService::searchFilters($request);
            $categories = CategoryService::getCategories();
            foreach ($products as $product) {
                $product->categories = CategoryService::getParentCategories($product->category_id, $categories);
            }
        }
        return view('home', [
            'maxProductsPrice' => ProductService::getMaxProductsPrice(),
            'categories' => CategoryService::getCategories(),
            'products' => $products
        ]);
    }
}
