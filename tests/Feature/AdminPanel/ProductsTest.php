<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\File;
use App\Models\Filter;
use App\Models\FilterOption;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
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

        $response = $this->actingAs(parent::getAdmin())
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

        $response = $this->actingAs(parent::getAdmin())
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

        $response = $this->actingAs(parent::getAdmin())->get('/admin-panel/products');

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
        $productImage = File::factory()->create([
            'product_id' => $product->id,
            'display_type' => 'productPhotosGallery',
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-products', ['category' => null]);
        $response2 = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-products', ['category' => ['id' => $category->id, 'name' => 'cname']]);
        $p = $response['products']['data'][1];

        $this->assertTrue(2 === count($response['products']['data']));
        $this->assertTrue(1 === count($response2['products']['data']));
        $this->assertTrue('title' === $p['title']);
        $this->assertTrue(Storage::url($productImage->url_thumbnail) === $p['product_images'][0]['fullUrlSmall']);
    }

    public function test_addProduct(): void
    {
        $category = Category::factory()->create();
        Storage::fake('public');
        $productImage = UploadedFile::fake()->image('product.jpg');
        $productImage2 = UploadedFile::fake()->image('product2.jpg');
        $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-product', [
            'title' => 'title',
            'slug' => 'slug',
            'description' => '{"time":1730888110020,"blocks":[{"id":"2phiNPo1Wx","type":"paragraph","data":{"text":"description"}}],"version":"2.30.6"}',
            'price' => 11,
            'quantity' => 22,
            'categoryId' => $category->id,
            'files' => [$productImage, $productImage2],
            'filesArr' => json_encode([['positionInInput' => 0], ['positionInInput' => 1]]),
        ]);
        $files = File::all();
        Storage::disk('public')->assertExists($files[0]->url);
        Storage::disk('public')->assertExists($files[1]->url);
        Storage::disk('public')->assertExists($files[0]->url_thumbnail);
        $this->assertDatabaseHas('products', [
            'title' => 'title',
        ]);
    }

    public function test_addProduct_edit(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'title' => 'title',
            'description' => '{"time":1730888110020,"blocks":[{"id":"2phiNPo1Wx","type":"paragraph","data":{"text":"description"}}],"version":"2.30.6"}',
            'price' => 11,
            'quantity' => 22,
            'category_id' => $category->id,
        ]);
        $fileDb = File::factory()->create([
            'url' => 'url',
            'url_thumbnail' => 'url_thumbnail',
            'position' => 0,
            'product_id' => $product->id,
        ]);
        Storage::fake('public');
        Storage::disk('public')->put('url2.jpg', 'content');
        Storage::disk('public')->put('small/url2.jpg', 'content');
        $fileDb2 = File::factory()->create([
            'url' => 'url2.jpg',
            'url_thumbnail' => 'small/url2.jpg',
            'position' => 1,
            'product_id' => $product->id,
        ]);
        $file = UploadedFile::fake()->image('product.jpg');

        $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-product', [
            'productId' => $product->id,
            'title' => 'titleEdited',
            'slug' => 'slug',
            'description' => '{"time":1730888110020,"blocks":[{"id":"2phiNPo1Wx","type":"paragraph","data":{"text":"descriptionEdited"}}],"version":"2.30.6"}',
            'price' => 111,
            'quantity' => 222,
            'categoryId' => $category->id,
            'files' => [$file],
            'filesArr' => json_encode([
                ['positionInInput' => 0],
                ['id' => $fileDb->id, 'removed' => false],
                ['id' => $fileDb2->id, 'removed' => true]
            ]),
        ]);
        $fileDbNew = File::whereNotIn('id', [$fileDb->id, $fileDb2->id])->first();
        $this->assertTrue($fileDb->fresh()->product_id === $product->id);
        $this->assertTrue($fileDb2->fresh()->product_id === null);
        $this->assertTrue($fileDbNew->product_id === $product->id);
        $this->assertDatabaseHas('products', [
            'title' => 'titleEdited',
        ]);
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

        $this->actingAs(parent::getAdmin())->postJson('/admin-panel/delete-products', [
            'productIds' => [
                $product->id,
                $product2->id,
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

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-product', [
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

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-product', [
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

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/delete-products', [
            'productIds' => [
                $product->id,
                $product2->id,
                $product3->id,
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('files', 7);
        $this->assertEquals(7, File::where('product_id', null)->count());
    }

    public function test_addProduct_attachments()
    {
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

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-product', [
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

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-product', [
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

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/delete-products', [
            'productIds' => [
                $product->id,
                $product2->id,
                $product3->id,
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('attachments', 7);
        $this->assertEquals(7, Attachment::where('product_id', null)->count());
    }
}
