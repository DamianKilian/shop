<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddProductRequest;
use App\Models\Product;
use App\Models\ProductPhoto;
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

    public function suggestions(Request $request)
    {
    }

    public function deleteProducts(Request $request)
    {
        DB::transaction(function () use ($request) {
            $this->deleteProductsDb($request);
        });
    }

    protected function deleteProductsDb($request)
    {
        foreach ($request->products as $product) {
            $productPhotosIds = [];
            foreach ($product['product_photos'] as $product_photo) {
                $productPhotosIds[] = $product_photo['id'];
            }
            if ($productPhotosIds) {
                ProductPhoto::where('product_id', $product['id'])
                    ->whereIn('id', $productPhotosIds)
                    ->delete();
            }
            Product::where('id', $product['id'])
                ->where('title', $product['title'])
                ->delete();
        }
    }

    public function addProduct(AddProductRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->createProduct($request);
        });
    }

    protected function createProduct($request)
    {
        if ($request->productId) {
            $product = Product::find($request->productId);
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => str_replace(',', '.', $request->price),
                'quantity' => $request->quantity,
            ]);
        } else {
            $product = Product::create([
                'title' => $request->title,
                'description' => $request->description,
                'price' => str_replace(',', '.', $request->price),
                'quantity' => $request->quantity,
                'category_id' => $request->categoryId,
            ]);
        }
        if ("[]" !== $request->filesArr) {
            $this->addImages($request, $product->id);
        }
        return $product->id;
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
                $this->updateImage($image, $productId, $position);
                continue;
            }
            $file = $image->file;
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
                'position' => $position,
                'size' => $file->getSize(),
                'product_id' => $productId,
            ]);
        }
    }

    protected function updateImage($image, $productId, $position)
    {
        $photo = ProductPhoto::where('id', $image->id)
            ->where('product_id', $productId)->first();
        if ($image->removed) {
            Storage::disk('public')->delete([
                $photo->url_small,
                $photo->url,
            ]);
            $photo->forceDelete();
        } else {
            $photo->update([
                'position' => $position,
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
        $products = ProductService::searchFilters($request);
        foreach ($products as &$product) {
            if (0 === $product->productPhotos->count()) {
                continue;
            }
            foreach ($product->productPhotos as &$photo) {
                $photo->fullUrlSmall = Storage::url($photo->url_small);
            }
            unset($photo);
        }
        unset($product);
        return response()->json([
            'products' => $products,
        ]);
    }
}
