<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Category;
use App\Models\Filter;
use App\Models\FilterOption;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\assertTrue;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getProductFilterOptions(): void
    {
        Filter::factory()->count(3)->create();
        $filter = Filter::factory()->create();
        $filter2 = Filter::factory()->create();
        $filterOption = FilterOption::factory()->create([
            'filter_id' => $filter->id,
        ]);
        $filterOption2 = FilterOption::factory()->create([
            'filter_id' => $filter->id,
        ]);
        $filterOption3 = FilterOption::factory()->create([
            'filter_id' => $filter2->id,
        ]);
        $categoryParent = Category::factory()->create();
        $category = Category::factory()->create([
            'parent_id' => $categoryParent->id
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $category->filters()->save($filter);
        $product->filterOptions()->save($filterOption);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/admin-panel/get-product-filter-options', [
                'categoryId' => $category->id,
                'productId' => $product->id,
            ]);

        $response->assertStatus(200);
        assertTrue($filterOption->id === $response['filterOptions'][0]);
    }

    public function test_products(): void
    {
        $category = Category::factory()->create([
            'parent_id' => null,
            'name' => 'Example testing category name',
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
        $category = Category::factory()->create();
        $category2 = Category::factory()->create();
        $product = Product::factory()->create([
            'title' => 'title',
            'category_id' => $category->id,
        ]);
        $product2 = Product::factory()->create([
            'title' => 'title2',
            'category_id' => $category2->id,
        ]);
        $productPhoto = ProductPhoto::factory()->create([
            'product_id' => $product->id,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/admin-panel/get-products', ['category' => null]);
        $response2 = $this->actingAs($user)->postJson('/admin-panel/get-products', ['category' => ['id' => $category->id, 'name' => 'cname']]);
        $p = $response['products']['data'][1];

        $this->assertTrue(2 === count($response['products']['data']));
        $this->assertTrue(1 === count($response2['products']['data']));
        $this->assertTrue('title' === $p['title']);
        $this->assertTrue(Storage::url($productPhoto->url_small) === $p['product_photos'][0]['fullUrlSmall']);
    }

    public function test_addProduct(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        Storage::fake('public');

        $productPhoto = UploadedFile::fake()->image('product.jpg');
        $productPhoto2 = UploadedFile::fake()->image('product2.jpg');
        $this->actingAs($user)->postJson('/admin-panel/add-product', [
            'title' => 'title',
            'slug' => 'slug',
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

    public function test_edit_addProduct(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'title' => 'title',
            'description' => 'description',
            'price' => 11,
            'quantity' => 22,
            'category_id' => $category->id,
        ]);
        $productPhotoDb = ProductPhoto::factory()->create([
            'url' => 'url',
            'url_small' => 'url_small',
            'position' => 0,
            'size' => 22,
            'product_id' => $product->id,
        ]);
        Storage::fake('public');
        Storage::disk('public')->put('url2.jpg', 'content');
        Storage::disk('public')->put('small/url2.jpg', 'content');
        $productPhotoDb2 = ProductPhoto::factory()->create([
            'url' => 'url2.jpg',
            'url_small' => 'small/url2.jpg',
            'position' => 1,
            'size' => 22,
            'product_id' => $product->id,
        ]);
        $user = User::factory()->create();
        $productPhoto = UploadedFile::fake()->image('product.jpg');

        $this->actingAs($user)->postJson('/admin-panel/add-product', [
            'productId' => $product->id,
            'title' => 'titleEdited',
            'slug' => 'slug',
            'description' => 'descriptionEdited',
            'price' => 111,
            'quantity' => 222,
            'categoryId' => $category->id,
            'files' => [$productPhoto],
            'filesArr' => json_encode([
                ['positionInInput' => 0],
                ['id' => $productPhotoDb->id, 'removed' => false],
                ['id' => $productPhotoDb2->id, 'removed' => true]
            ]),
        ]);

        Storage::disk('public')->assertExists('products/' . $productPhoto->hashName());
        Storage::disk('public')->assertExists('products/small/' . $productPhoto->hashName());
        Storage::disk('public')->assertMissing('url2.jpg');
        Storage::disk('public')->assertMissing('small/url2.jpg');
        $this->assertDatabaseHas('products', [
            'title' => 'titleEdited',
        ]);
        $this->assertDatabaseHas('product_photos', [
            'url' => 'products/' . $productPhoto->hashName(),
        ]);
        $this->assertDatabaseHas('product_photos', [
            'id' => $productPhotoDb->id,
            'position' => 1,
        ]);
        $this->assertModelMissing($productPhotoDb2);
    }

    public function test_deleteProducts(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $product3 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/admin-panel/delete-products', [
            'products' => [
                ['id' => $product->id,],
                ['id' => $product2->id,],
            ]
        ]);

        $this->assertSoftDeleted($product);
        $this->assertSoftDeleted($product2);
        $this->assertNotSoftDeleted($product3);
    }
}
