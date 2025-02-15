<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class BasketTest extends TestCase
{
    use RefreshDatabase;

    public function test_basketIndex(): void
    {
        $response = $this->get("/basket/index");

        $response->assertViewHas('deliveryMethods', function ($deliveryMethods) {
            $deliveryMethodsArr = json_decode($deliveryMethods, true);
            $inpost = [
                'name' => 'InPost',
                'price' => 0
            ];
            return $inpost == $deliveryMethodsArr['inpost'];
        });
        $response->assertSee('InPost');
        $response->assertSuccessful();
    }

    public function test_getProductsInBasketData(): void
    {
        $category = Category::factory()->create([
            'slug' => 'milk',
        ]);
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);
        $p1 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $p2  = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $productsInBasket = [
            $p1->id => ['num' => 1],
            $p2->id => ['num' => 1],
        ];

        $response = $this->postJson('/api/basket/get-products-in-basket-data', [
            'productsInBasket' => $productsInBasket
        ]);

        $response->assertSuccessful();
        assertTrue(isset($response['productsInBasketData'][$p1->id]));
        assertTrue(isset($response['productsInBasketData'][$p2->id]));
        assertTrue(2 === count($response['productsInBasketData']));
    }
}
