<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddProductRequest;
use App\Models\Attachment;
use App\Models\File;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\EditorJSService;
use App\Services\FileService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\Image\Image;

class AdminPanelProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addOptionsToSelectedProducts(Request $request)
    {
        $productIds = [];
        $checkedOptionIds = [];
        foreach ($request->products as $product) {
            $productIds[] = $product['id'];
        }
        foreach ($request->checkedOptionsAllFilters as $checkedOptions) {
            $checkedOptionIds = array_merge($checkedOptionIds, $checkedOptions);
        }
        $products = Product::whereIn('id', $productIds)->get();
        foreach ($products as $productDb) {
            if ($request->remove) {
                $productDb->filterOptions()->detach($checkedOptionIds);
            } else {
                $productDb->filterOptions()->attach($checkedOptionIds);
            }
        }
    }

    public function deleteProducts(Request $request)
    {
        foreach ($request->products as $product) {
            $productIds[] = $product['id'];
        }
        File::whereIn('product_id', $productIds)
            ->update(['product_id' => null]);
        Attachment::whereIn('product_id', $productIds)
            ->update(['product_id' => null]);
        Product::whereIn('id', $productIds)->delete();
    }

    public function getProductDesc(Request $request)
    {
        $desc = Product::select('description')->find($request->productId)->description;
        return response()->json([
            'desc' => $desc,
            'productId' => $request->productId,
        ]);
    }

    public function getProductFilterOptions(Request $request)
    {
        $filters = [];
        $filterOptions = [];
        $categories = CategoryService::getCategories();
        $parentCategoriesIds = array_keys(CategoryService::getParentCategories($request->categoryId, $categories));
        $filters = CategoryService::getCategoryFilters($parentCategoriesIds);
        if ($request->productId) {
            $filterOptions = Product::find($request->productId)->filterOptions()->get()->pluck('id');
        }
        return response()->json([
            'filters' => $filters,
            'filterOptions' => $filterOptions,
            'categoryId' => $request->categoryId,
            'productId' => $request->productId,
        ]);
    }

    public function addProduct(AddProductRequest $request)
    {
        $product = null;
        DB::transaction(function () use ($request, &$product) {
            $product = $this->createProduct($request);
        });
        $productId = $product ? $product->id : null;
        return response()->json([
            'productId' => $productId,
        ]);
    }

    protected function createProduct($request)
    {
        if ($request->productId) {
            $product = Product::find($request->productId);
            $product->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'description_str' => ProductService::getProductDescStr($request->description),
                'price' => str_replace(',', '.', $request->price),
                'quantity' => $request->quantity,
                'category_id' => $request->categoryId,
            ]);
        } else {
            $product = Product::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'description_str' => ProductService::getProductDescStr($request->description),
                'price' => str_replace(',', '.', $request->price),
                'quantity' => $request->quantity,
                'category_id' => $request->categoryId,
            ]);
        }
        if ("[]" !== $request->filesArr) {
            $this->addImages($request, $product->id);
        }
        $product->filterOptions()->sync($request->filterOptions);
        EditorJSService::resetPageImages($product, 'product', update: !!$request->productId);
        EditorJSService::resetPageGalleryImages($product, 'product', update: !!$request->productId);
        EditorJSService::resetAttachments($product, 'product', update: !!$request->productId);
        return $product;
    }

    protected function addImages($request, $productId)
    {
        $filesArr = json_decode($request->filesArr);
        if ($request->file('files')) {
            foreach ($request->file('files') as $positionInInput => $file) {
                foreach ($filesArr as &$image) {
                    if (isset($image->positionInInput) && $image->positionInInput === $positionInInput) {
                        $image->file = $file;
                    }
                }
                unset($image);
            }
        }
        $position = -1;
        foreach ($filesArr as $image) {
            $position += 1;
            if (isset($image->id)) {
                $this->updateImage($image, $position);
                continue;
            }
            FileService::saveFile(
                $image->file,
                displayType: 'productPhotosGallery',
                fileType: 'image',
                thumbnail: true,
                position: $position,
                maxWidth: 1920 * 3,
                productId: $productId,
            );
        }
    }

    protected function updateImage($image, $position)
    {
        $photo = File::whereId($image->id)->first();
        if ($image->removed) {
            $photo->update([
                'product_id' => null,
            ]);
        } else {
            $photo->update([
                'position' => $position,
            ]);
        }
    }

    public function products()
    {
        $categories = Category::orderBy('parent_id')->orderBy('position')->get();
        $categoryOptions = CategoryService::categoryOptions($categories);
        return view('adminPanel.products', [
            'categoryOptions' => json_encode($categoryOptions),
            'categories' => $categories,
        ]);
    }

    public function getProducts(Request $request)
    {
        $categoryChildrenIds = [];
        if ($request->category) {
            $categoryChildrenIds = CategoryService::getCategoryChildrenIds([$request->category['id']]);
        }
        $products = ProductService::searchFilters($request, $categoryChildrenIds, true, 20, ['limit' => 30]);
        $productsArr  = $products->toArray();
        foreach ($productsArr['data'] as &$product) {
            $product['description_str'] = null;
        }
        unset($product);
        return response()->json([
            'products' => $productsArr,
        ]);
    }
}
