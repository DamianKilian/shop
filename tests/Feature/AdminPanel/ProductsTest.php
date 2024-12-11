<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\File;
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

    public function test_getProductDesc(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/admin-panel/get-product-desc', [
                'productId' => $product->id,
            ]);

        $response->assertStatus(200);
        assertTrue($product->id === $response['productId']);
        assertTrue($product->description === $response['desc']);
    }

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
            'description' => '{"time":1730888110020,"blocks":[{"id":"2phiNPo1Wx","type":"paragraph","data":{"text":"description"}}],"version":"2.30.6"}',
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
            'description' => '{"time":1730888110020,"blocks":[{"id":"2phiNPo1Wx","type":"paragraph","data":{"text":"description"}}],"version":"2.30.6"}',
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
            'description' => '{"time":1730888110020,"blocks":[{"id":"2phiNPo1Wx","type":"paragraph","data":{"text":"descriptionEdited"}}],"version":"2.30.6"}',
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
        $file = File::factory()->create([
            'product_id' => $product->id,
        ]);
        $attachment = Attachment::factory()->create([
            'product_id' => $product2->id,
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
        $this->assertTrue(null ===  $file->productId);
        $this->assertTrue(null ===  $attachment->productId);
    }

    public function test_addProduct_images()
    {
        $this->addProduct_images();
    }
    public function test_addProduct_imagesGallery()
    {
        $this->addProduct_images('gallery');
    }
    public function test_edit_addProduct_images()
    {
        $this->edit_addProduct_images();
    }
    public function test_edit_addProduct_imagesGallery()
    {
        $this->edit_addProduct_images('gallery');
    }
    public function test_deleteProducts_images()
    {
        $this->deleteProducts_images();
    }
    public function test_deleteProducts_imagesGallery()
    {
        $this->deleteProducts_images('gallery');
    }

    protected function getDescArr($urls, $displayType = '')
    {
        $descArr = array(
            'time' => 1729269060460,
            'version' => '2.30.6',
        );
        $blocks = array(array(
            'id' => 'gM2YmfoYJC',
            'type' => 'paragraph',
            'data' => array('text' => 'aaaa',),
        ), array(
            'id' => 'yJ7a1OpjJo',
            'type' => 'paragraph',
            'data' => array('text' => 'bbbb',),
        ), array(
            'id' => 'knvHiCRklt',
            'type' => 'paragraph',
            'data' => array('text' => 'cccc',),
        ),);
        $blocksType = [];
        if ('image' === $displayType) {
            foreach ($urls as $url) {
                $blocksType[] = [
                    'id' => 'AMFSAziZvQ',
                    'type' => 'image',
                    'data' => array(
                        'caption' => 'dddd',
                        'withBorder' => false,
                        'withBackground' => false,
                        'stretched' => false,
                        'file' => array(
                            'url' => env('APP_URL') . '/storage/' . $url,
                            'urlDb' => $url,
                        ),
                    ),
                ];
            }
        } elseif ('gallery' === $displayType) {
            $items = [];
            foreach ($urls as $url) {
                $items[] = [
                    "url" => $url,
                    "caption" => "ccc"
                ];
            }
            $blocksType[] = [
                "id" => "SAfSXhLpv1",
                "type" => "gallery",
                "data" => [
                    "items" => $items,
                    "config" => "standard",
                    "countItemEachRow" => "1"
                ]
            ];
        } elseif ('' === $displayType) {
            foreach ($urls as $url) {
                $blocksType[] = [
                    'id' => 'kW24e62oTp',
                    'type' => 'attaches',
                    'data' => [
                        'title' => 'ttt',
                        'file' => array(
                            'url' => env('APP_URL') . '/storage/' . $url,
                            'urlDb' => $url,
                        ),
                    ],
                ];
            }
        }
        $descArr['blocks'] = array_merge($blocks, $blocksType);
        return $descArr;
    }

    protected function addProduct_images($displayType = 'image')
    {
        $user = User::factory()->create();
        $urlDb1 = 'products/urlDb1.jpg';
        $urlDb2 = 'products/urlDb2.jpg';
        File::factory()->count(3)->create([
            'display_type' => $displayType,
        ]);
        File::factory()->create([
            'url' => $urlDb1,
            'display_type' => $displayType,
        ]);
        File::factory()->create([
            'url' => $urlDb2,
            'display_type' => $displayType,
        ]);
        File::factory()->create([
            'url' => $urlDb2,
            'display_type' => $displayType,
        ]);
        $descriptionArray = $this->getDescArr([$urlDb1, $urlDb2], $displayType);
        $description = json_encode($descriptionArray, JSON_UNESCAPED_SLASHES);
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->postJson('/admin-panel/add-product', [
            'title' => 'title',
            'slug' => 'slug',
            'description' => $description,
            'price' => 11,
            'quantity' => 22,
            'categoryId' => $category->id,
            'files' => [],
            'filesArr' => json_encode([]),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('files', 6);
        $this->assertEquals(2, File::where('product_id', $response['productId'])->count());
        $this->assertDatabaseHas('files', [
            'url' => $urlDb1,
            'product_id' => $response['productId'],
            'page_id' => null,
        ]);
        $this->assertDatabaseHas('files', [
            'url' => $urlDb2,
            'product_id' => $response['productId'],
            'page_id' => null,
        ]);
    }

    protected function edit_addProduct_images($displayType = 'image')
    {
        $user = User::factory()->create();
        $urlDbOld = 'products/urlDbOld.jpg';
        $urlDbNew = 'products/urlDbNew.jpg';
        $urlDbRemoved = 'products/urlDbRemoved.jpg';
        $descriptionArray = $this->getDescArr([$urlDbOld, $urlDbRemoved], $displayType);
        $description = json_encode($descriptionArray, JSON_UNESCAPED_SLASHES);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'title' => 'title',
            'description' => $description,
            'price' => 11,
            'quantity' => 22,
            'category_id' => $category->id,
        ]);
        File::factory()->count(3)->create([
            'display_type' => $displayType,
        ]);
        File::factory()->create([
            'url' => $urlDbOld,
            'product_id' => $product->id,
            'display_type' => $displayType,
        ]);
        File::factory()->create([
            'url' => $urlDbOld,
            'display_type' => $displayType,
        ]);
        File::factory()->count(2)->create([
            'url' => $urlDbNew,
            'display_type' => $displayType,
        ]);
        File::factory()->create([
            'url' => $urlDbRemoved,
            'product_id' => $product->id,
            'display_type' => $displayType,
        ]);
        File::factory()->create([
            'url' => $urlDbRemoved,
            'display_type' => $displayType,
        ]);
        $descriptionArrayNew = $this->getDescArr([$urlDbOld, $urlDbNew], $displayType);
        $descriptionNew = json_encode($descriptionArrayNew, JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs($user)->postJson('/admin-panel/add-product', [
            'productId' => $product->id,
            'title' => 'titleEdited',
            'slug' => 'slug',
            'description' => $descriptionNew,
            'price' => 111,
            'quantity' => 222,
            'categoryId' => $category->id,
            'files' => [],
            'filesArr' => json_encode([]),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('files', 9);
        $this->assertEquals(2, File::where('product_id', $product->id)->count());
        $this->assertDatabaseHas('files', [
            'url' => $urlDbOld,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseHas('files', [
            'url' => $urlDbNew,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseMissing('files', [
            'url' => $urlDbRemoved,
            'product_id' => $product->id,
        ]);
    }

    protected function deleteProducts_images($displayType = 'image')
    {
        $user = User::factory()->create();
        $urlDb = ['products/urlDb1.jpg', 'products/urlDb2.jpg', 'products/urlDb3.jpg', 'products/urlDb4.jpg'];
        for ($x = 1; $x <= 2; $x++) {
            $descriptionArray[$x] = $this->getDescArr([$urlDb[$x], $urlDb[$x + 1]], $displayType);
        }
        $description = json_encode($descriptionArray[1], JSON_UNESCAPED_SLASHES);
        $description2 = json_encode($descriptionArray[2], JSON_UNESCAPED_SLASHES);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'description' => $description,
            'category_id' => $category->id,
        ]);
        $product2 = Product::factory()->create([
            'description' => $description2,
            'category_id' => $category->id,
        ]);
        $product3 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        File::factory()->count(3)->create([
            'display_type' => $displayType,
        ]);
        foreach ($urlDb as $key => $value) {
            if ($key < 2) {
                File::factory()->create([
                    'url' => $value,
                    'product_id' => $product->id,
                    'display_type' => $displayType,
                ]);
            } else {
                File::factory()->create([
                    'url' => $value,
                    'product_id' => $product2->id,
                    'display_type' => $displayType,
                ]);
            }
        }

        $response = $this->actingAs($user)->postJson('/admin-panel/delete-products', [
            'products' => [
                ['id' => $product->id,],
                ['id' => $product2->id,],
                ['id' => $product3->id,],
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('files', 7);
        $this->assertEquals(7, File::where('product_id', null)->count());
    }

    public function test_addProduct_attachments()
    {
        $user = User::factory()->create();
        $urlDb1 = 'attachments/urlDb1.jpg';
        $urlDb2 = 'attachments/urlDb2.jpg';
        Attachment::factory()->count(3)->create();
        Attachment::factory()->create([
            'url' => $urlDb1,
        ]);
        Attachment::factory()->create([
            'url' => $urlDb2,
        ]);
        Attachment::factory()->create([
            'url' => $urlDb2,
        ]);
        $descriptionArray = $this->getDescArr([$urlDb1, $urlDb2]);
        $description = json_encode($descriptionArray, JSON_UNESCAPED_SLASHES);
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->postJson('/admin-panel/add-product', [
            'title' => 'title',
            'slug' => 'slug',
            'description' => $description,
            'price' => 11,
            'quantity' => 22,
            'categoryId' => $category->id,
            'files' => [],
            'filesArr' => json_encode([]),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('attachments', 6);
        $this->assertEquals(2, Attachment::where('product_id', $response['productId'])->count());
        $this->assertDatabaseHas('attachments', [
            'url' => $urlDb1,
            'product_id' => $response['productId'],
            'page_id' => null,
        ]);
        $this->assertDatabaseHas('attachments', [
            'url' => $urlDb2,
            'product_id' => $response['productId'],
            'page_id' => null,
        ]);
    }

    public function test_edit_addProduct_attachments()
    {
        $user = User::factory()->create();
        $urlDbOld = 'attachments/urlDbOld.jpg';
        $urlDbNew = 'attachments/urlDbNew.jpg';
        $urlDbRemoved = 'attachments/urlDbRemoved.jpg';
        $descriptionArray = $this->getDescArr([$urlDbOld, $urlDbRemoved]);
        $description = json_encode($descriptionArray, JSON_UNESCAPED_SLASHES);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'title' => 'title',
            'description' => $description,
            'price' => 11,
            'quantity' => 22,
            'category_id' => $category->id,
        ]);
        Attachment::factory()->count(3)->create();
        Attachment::factory()->create([
            'url' => $urlDbOld,
            'product_id' => $product->id,
        ]);
        Attachment::factory()->create([
            'url' => $urlDbOld,
        ]);
        Attachment::factory()->count(2)->create([
            'url' => $urlDbNew,
        ]);
        Attachment::factory()->create([
            'url' => $urlDbRemoved,
            'product_id' => $product->id,
        ]);
        Attachment::factory()->create([
            'url' => $urlDbRemoved,
        ]);
        $descriptionArrayNew = $this->getDescArr([$urlDbOld, $urlDbNew]);
        $descriptionNew = json_encode($descriptionArrayNew, JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs($user)->postJson('/admin-panel/add-product', [
            'productId' => $product->id,
            'title' => 'titleEdited',
            'slug' => 'slug',
            'description' => $descriptionNew,
            'price' => 111,
            'quantity' => 222,
            'categoryId' => $category->id,
            'files' => [],
            'filesArr' => json_encode([]),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('attachments', 9);
        $this->assertEquals(2, Attachment::where('product_id', $product->id)->count());
        $this->assertDatabaseHas('attachments', [
            'url' => $urlDbOld,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseHas('attachments', [
            'url' => $urlDbNew,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseMissing('attachments', [
            'url' => $urlDbRemoved,
            'product_id' => $product->id,
        ]);
    }

    public function test_deleteProducts_attachments()
    {
        $user = User::factory()->create();
        $urlDb = ['attachments/urlDb1.jpg', 'attachments/urlDb2.jpg', 'attachments/urlDb3.jpg', 'attachments/urlDb4.jpg'];
        for ($x = 1; $x <= 2; $x++) {
            $descriptionArray[$x] = $this->getDescArr([$urlDb[$x], $urlDb[$x + 1]]);
        }
        $description = json_encode($descriptionArray[1], JSON_UNESCAPED_SLASHES);
        $description2 = json_encode($descriptionArray[2], JSON_UNESCAPED_SLASHES);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'description' => $description,
            'category_id' => $category->id,
        ]);
        $product2 = Product::factory()->create([
            'description' => $description2,
            'category_id' => $category->id,
        ]);
        $product3 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        Attachment::factory()->count(3)->create();
        foreach ($urlDb as $key => $value) {
            if ($key < 2) {
                Attachment::factory()->create([
                    'url' => $value,
                    'product_id' => $product->id,
                ]);
            } else {
                Attachment::factory()->create([
                    'url' => $value,
                    'product_id' => $product2->id,
                ]);
            }
        }

        $response = $this->actingAs($user)->postJson('/admin-panel/delete-products', [
            'products' => [
                ['id' => $product->id,],
                ['id' => $product2->id,],
                ['id' => $product3->id,],
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('attachments', 7);
        $this->assertEquals(7, Attachment::where('product_id', null)->count());
    }
}
