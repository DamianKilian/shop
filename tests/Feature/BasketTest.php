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

    public function test_getBasketSummary(): void
    {
        $category = Category::factory()->create([
            'slug' => 'milk',
        ]);
        $p1 = Product::factory()->create([
            'price' => 10.10,
            'category_id' => $category->id,
        ]);
        $p2  = Product::factory()->create([
            'price' => 100.20,
            'category_id' => $category->id,
        ]);
        $p3  = Product::factory()->create([
            'price' => 1000.30,
            'category_id' => $category->id,
        ]);
        $expected = [
            "productsPrice" => "1 231,00",
            "deliveryPrice" => "0,00",
            "totalPrice" => "1 231,00",
        ];

        $response = $this->post("/api/basket/get-basket-summary", [
            'productsInBasket' => [
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ],
            'deliveryMethod' => 'inpost',
        ]);

        $response->assertSuccessful();
        $this->assertTrue($response['summary'] === $expected);
    }

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
