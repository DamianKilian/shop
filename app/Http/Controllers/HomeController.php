<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetProductsViewRequest;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Suggestion;
use App\Services\AppService;
use App\Services\CategoryService;
use App\Services\EditorJSService;
use App\Services\ProductService;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function getSuggestions(Request $request)
    {
        $suggestions = Suggestion::whereFullText('suggestion', $request->searchValue)
            ->orWhere('suggestion', 'LIKE', '%' . $request->searchValue . '%')
            ->get();
        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }

    public function category(Request $request, EditorJSService $editorJS, $slug)
    {
        $categories = CategoryService::getCategories();
        $category = Category::where('slug', $slug)->first();
        if (null === $request->categoryChildrenIds) {
            $categoryChildrenIds = CategoryService::getCategoryChildrenIds([$category->id], $categories);
        } else {
            $categoryChildrenIds = json_decode($request->categoryChildrenIds);
        }
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
            'footerHtml' => AppService::getFooterHtml($editorJS),
        ]);
    }

    public function getProductsView(GetProductsViewRequest $request)
    {
        $categoryChildrenIds = json_decode($request->categoryChildrenIds);
        $products = ProductService::searchFilters($request, $categoryChildrenIds);
        SearchService::addSuggestion($request, $products);
        return view('_partials.products', [
            'products' => $products
        ]);
    }

    public function getProductsViewAllCategories(GetProductsViewRequest $request)
    {
        $products = ProductService::searchFilters($request);
        SearchService::addSuggestion($request, $products);
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

    public function index(Request $request, EditorJSService $editorJS, $slug = null)
    {
        $products = null;
        $categories = CategoryService::getCategories();
        if ($request->searchValue) {
            $products = ProductService::searchFilters($request);
            foreach ($products as $product) {
                $product->categories = CategoryService::getParentCategories($product->category_id, $categories);
            }
        }
        if ($slug === env('PREVIEW_SLUG') && !Auth::check()) {
            return redirect()->guest('login');
        }
        $page = Page::whereSlug($slug)->whereActive(true)->first();
        if (!$page) {
            abort(404);
        }
        if ($page->body_prod) {
            $page->bodyHtml = $editorJS->toHtml($page->body_prod);
        }
        return view('home', [
            'page' => $page,
            'maxProductsPrice' => ProductService::getMaxProductsPrice(),
            'categories' => $categories,
            'products' => $products,
            'footerHtml' => AppService::getFooterHtml($editorJS),
        ]);
    }

    public function product(Request $request, EditorJSService $editorJS, $slug)
    {
        $product = Product::where('slug', $slug)->first();
        if ($product->description) {
            $product->bodyHtml = $editorJS->toHtml($product->description);
        }
        return view('product', [
            'product' => $product,
            'searchUrl' => route('home'),
            'footerHtml' => AppService::getFooterHtml($editorJS),
        ]);
    }
}
