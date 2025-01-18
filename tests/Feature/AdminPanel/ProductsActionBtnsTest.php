<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class ProductsActionBtnsTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggleActiveProduct(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'active' => false,
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/toggle-active-product', [
            'productId' => $product->id,
            'active' => true,
        ]);
        $product = Product::whereId($product->id)->first();

        $response->assertStatus(200);
        $this->assertTrue(1 === $product->active);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/toggle-active-product', [
            'productId' => $product->id,
            'active' => false,
        ]);
        $product = Product::whereId($product->id)->first();

        $this->assertTrue(0 === $product->active);
    }

    public function test_applyChangesProduct(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'description' => 'description',
            'description_prod' => 'description_prod',
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/apply-changes-product', [
            'productId' => $product->id,
        ]);
        $product = Product::whereId($product->id)->first();

        $response->assertStatus(200);
        $this->assertTrue('description' === $product->description_prod);
    }

    public function test_addProduct_preview(): void
    {
        $descriptionArray = array(
            'time' => 1729269060460,
            'blocks' => array(0 => array(
                'id' => 'gM2YmfoYJC',
                'type' => 'paragraph',
                'data' => array('text' => 'aaaa',),
            ), 1 => array(
                'id' => 'yJ7a1OpjJo',
                'type' => 'paragraph',
                'data' => array('text' => 'bbbb',),
            ),),
            'version' => '2.30.6',
        );
        $description = json_encode($descriptionArray, JSON_UNESCAPED_SLASHES);
        $category = Category::factory()->create();

        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/add-product', [
            'title' => 'title',
            'slug' => 'slug',
            'price' => 11,
            'quantity' => 22,
            'categoryId' => $category->id,
            'preview' => 'true',
            'description' => $description,
            'files' => [],
            'filesArr' => json_encode([]),
        ]);
        $previewProduct = Product::whereSlug(env('PREVIEW_SLUG'))->first();

        $response
            ->assertStatus(200)
            ->assertJson([
                'previewUrl' => route('product', ['slug' => env('PREVIEW_SLUG')]),
            ]);
        $this->assertEquals($previewProduct->description_prod, $description);
        $this->assertEquals($previewProduct->title, 'title');
    }
}
