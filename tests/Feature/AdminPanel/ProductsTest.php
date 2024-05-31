<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_products(): void
    {
        $category = Category::create([
            'parent_id' => null,
            'name' => 'Example testing category name',
            'position' => 1,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin-panel/products');

        $response->assertStatus(200);
        $response->assertViewHas('categories', function ($collection) use ($category) {
            return $collection->contains($category);
        });
    }

    public function test_getProducts(): void
    {
        $category = Category::create([
            'parent_id' => null,
            'name' => 'Example testing category name',
            'position' => 0,
        ]);
        $product = Product::create([
            'title' => 'title',
            'description' => 'description',
            'price' => '111',
            'quantity' => '22',
            'category_id' => $category->id,
        ]);
        $productPhoto = ProductPhoto::create([
            'url' => 'url',
            'url_small' => 'url-small',
            'position' => 0,
            'size' => 111,
            'product_id' => $product->id,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/admin-panel/get-products', []);

        $p = $response['products'][0];
        $this->assertTrue('title' === $p['title']);
        $this->assertTrue(Storage::url($productPhoto->url_small) === $p['product_photos'][0]['fullUrlSmall']);
    }

    public function test_addProduct(): void
    {
        $category = Category::create([
            'parent_id' => null,
            'name' => 'Example testing category name',
            'position' => 0,
        ]);
        $user = User::factory()->create();
        Storage::fake('public');
        $productPhoto = UploadedFile::fake()->image('product.jpg');
        $productPhoto2 = UploadedFile::fake()->image('product2.jpg');
        $this->actingAs($user)->postJson('/admin-panel/add-product', [
            'title' => 'title',
            'description' => 'description',
            'price' => 11,
            'quantity' => 22,
            'categoryId' => $category->id,
            'files' => [$productPhoto, $productPhoto2],
            'filesArr' => json_encode([['positionInInput' => 0], ['positionInInput' => 1]]),
        ]);
        Storage::disk('public')->assertExists('products/' . $productPhoto->hashName());
        Storage::disk('public')->assertExists('products/' . $productPhoto2->hashName());
        Storage::disk('public')->assertExists('products/small/' . $productPhoto->hashName());
        $this->assertDatabaseHas('products', [
            'title' => 'title',
        ]);
        $this->assertDatabaseHas('product_photos', [
            'url' => 'products/' . $productPhoto->hashName(),
        ]);
    }

    public function test_deleteProducts(): void
    {
        $category = Category::create([
            'parent_id' => null,
            'name' => 'Example testing category name',
            'position' => 0,
        ]);
        $product = Product::create([
            'title' => 'title',
            'description' => 'description',
            'price' => '111',
            'quantity' => '22',
            'category_id' => $category->id,
        ]);
        $product2 = Product::create([
            'title' => 'title2',
            'description' => 'description2',
            'price' => '111',
            'quantity' => '22',
            'category_id' => $category->id,
        ]);
        $product3 = Product::create([
            'title' => 'title3',
            'description' => 'description3',
            'price' => '111',
            'quantity' => '22',
            'category_id' => $category->id,
        ]);
        $productPhoto = ProductPhoto::create([
            'url' => 'url',
            'url_small' => 'url-small',
            'position' => 0,
            'size' => 111,
            'product_id' => $product->id,
        ]);
        $productPhoto2 = ProductPhoto::create([
            'url' => 'url1',
            'url_small' => 'url-small2',
            'position' => 0,
            'size' => 111,
            'product_id' => $product->id,
        ]);
        $productPhoto3 = ProductPhoto::create([
            'url' => 'url1',
            'url_small' => 'url-small2',
            'position' => 0,
            'size' => 111,
            'product_id' => $product2->id,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/admin-panel/delete-products', [
            'products' => [
                [
                    'id' => $product->id,
                    'title' => 'title',
                    'product_photos' => [
                        ['id' => $productPhoto->id],
                        ['id' => $productPhoto2->id]
                    ]
                ],
                [
                    'id' => $product2->id,
                    'title' => 'title2',
                    'product_photos' => []
                ],
            ]
        ]);

        $this->assertSoftDeleted($product);
        $this->assertSoftDeleted($product2);
        $this->assertSoftDeleted($productPhoto);
        $this->assertSoftDeleted($productPhoto2);
    }
}
