<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCategoriesRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddProductRequest;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\Image\Image;

class AdminPanelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addProduct(AddProductRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->createProduct($request);
        });
    }

    protected function createProduct($request)
    {
        $product = Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => str_replace(',', '.', $request->price),
            'quantity' => $request->quantity,
            'category_id' => $request->categoryId,
        ]);
        $filesArr = json_decode($request->filesArr);
        foreach ($request->file('files') as $key => $file) {
            foreach ($filesArr as &$value) {
                if ($value->positionInInput === $key) {
                    $value->file = $file;
                }
            }
            unset($value);
        }
        $position = 0;
        $productId = $product->id;
        foreach ($filesArr as $value) {
            $file = $value->file;
            $name = $file->hashName();
            $publicStorage = Storage::disk('public');
            $url = "products/$name";
            $urlAbsolute = $publicStorage->path($url);
            $urlSmall = "products/small/$name";
            $urlSmallAbsolute = $publicStorage->path($urlSmall);
            $publicStorage->put("products", $file);
            $publicStorage->copy($url, $urlSmall);
            Image::load($urlSmallAbsolute)
                ->width(400)
                ->height(400)
                ->save();
            ImageOptimizer::optimize($urlAbsolute);
            ImageOptimizer::optimize($urlSmallAbsolute);
            ProductPhoto::create([
                'url' => $url,
                'url_small' => $urlSmall,
                'position' => $position++,
                'size' => $file->getSize(),
                'product_id' => $productId,
            ]);
        }
    }

    public function products()
    {
        return view('adminPanel.products', [
            'categories' => Category::orderBy('parent_id')->orderBy('position')->get(),
        ]);
    }

    public function getProducts(Request $request)
    {
        return response()->json([
            'products' => [],
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
