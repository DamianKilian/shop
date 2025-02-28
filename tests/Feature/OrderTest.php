<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_orderStore(): void
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

        $response = $this->post("/order/store", [
            'productsInBasket' => json_encode([
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ]),
            'deliveryMethod' => 'inpost',
        ]);
        $order = Order::with('products')->latest()->first();

        assertTrue(3 === $order->products()->count());
        $response->assertSessionHas('summary', function ($value) {
            return $value['formatted'] == [
                "productsPrice" => "1 231,00",
                "deliveryPrice" => "0,00",
                "totalPrice" => "1 231,00",
            ];
        });
        $response->assertSessionHas('productsInBasketArr', function ($value) use ($p1, $p2, $p3) {
            return $value == [
                $p1->id => ["num" => 3],
                $p2->id => ["num" => 2],
                $p3->id => ["num" => 1],
            ];
        });
        $response->assertSessionHas('deliveryMethod', function ($value) {
            return $value == '{"name":"InPost","price":"0"}';
        });
        $response->assertSessionHas('orderPaymentAccess', function ($value) use ($order) {
            return $value == $order->id;
        });
        $response->assertRedirect();
    }

    public function test_orderPayment_followingRedirect(): void
    {
        $category = Category::factory()->create([
            'slug' => 'milk',
        ]);
        $p1 = Product::factory()->create([
            'price' => 10.10,
            'category_id' => $category->id,
            'title' => 'title1',
        ]);
        $p2  = Product::factory()->create([
            'price' => 100.20,
            'category_id' => $category->id,
        ]);
        $p3  = Product::factory()->create([
            'price' => 1000.30,
            'category_id' => $category->id,
        ]);

        $this->followingRedirects();
        $response = $this->post("/order/store", [
            'productsInBasket' => json_encode([
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ]),
            'deliveryMethod' => 'inpost',
        ]);
        $order = Order::latest()->first(['id']);
        $productsInBasketData = json_decode($response['productsInBasketData'], true);
        $summary = json_decode($response['summary'], true);
        $deliveryMethod = json_decode($response['deliveryMethod'], true);

        $response->assertStatus(200);
        assertTrue('title1' === $productsInBasketData[$p1->id]['title']);
        assertTrue('1 231,00' === $summary['totalPrice']);
        assertTrue('InPost' === $deliveryMethod['name']);
        assertTrue($order->id === session()->get('orderPaymentAccess'));
    }

    public function test_orderPayment(): void
    {
        $category = Category::factory()->create([
            'slug' => 'milk',
        ]);
        $p1 = Product::factory()->create([
            'price' => 10.10,
            'category_id' => $category->id,
            'title' => 'title1',
        ]);
        $p2  = Product::factory()->create([
            'price' => 100.20,
            'category_id' => $category->id,
        ]);
        $p3  = Product::factory()->create([
            'price' => 1000.30,
            'category_id' => $category->id,
        ]);
        $user = User::factory()->make();
        $order = Order::create([
            'price' => 1231.00,
            'delivery_method' => 'inpost',
            'user_id' => $user->id,
        ]);
        $order->products()->attach([
            $p1->id => ['num' => 3],
            $p2->id => ['num' => 2],
            $p3->id => ['num' => 1],
        ]);

        $response = $this->actingAs($user)->get("/order/payment/$order->id", [
            'productsInBasket' => json_encode([
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ]),
            'deliveryMethod' => 'inpost',
        ]);
        $productsInBasketData = json_decode($response['productsInBasketData'], true);
        $summary = json_decode($response['summary'], true);
        $deliveryMethod = json_decode($response['deliveryMethod'], true);

        $response->assertStatus(200);
        assertTrue('title1' === $productsInBasketData[$p1->id]['title']);
        assertTrue('1 231,00' === $summary['totalPrice']);
        assertTrue('InPost' === $deliveryMethod['name']);
    }

    public function test_orderPayment_guest_cant_access(): void
    {
        $category = Category::factory()->create([
            'slug' => 'milk',
        ]);
        $p1 = Product::factory()->create([
            'price' => 10.10,
            'category_id' => $category->id,
            'title' => 'title1',
        ]);
        $p2  = Product::factory()->create([
            'price' => 100.20,
            'category_id' => $category->id,
        ]);
        $p3  = Product::factory()->create([
            'price' => 1000.30,
            'category_id' => $category->id,
        ]);
        $order = Order::create([
            'price' => 1231.00,
            'delivery_method' => 'inpost',
        ]);
        $order->products()->attach([
            $p1->id => ['num' => 3],
            $p2->id => ['num' => 2],
            $p3->id => ['num' => 1],
        ]);

        $response = $this->get("/order/payment/$order->id", [
            'productsInBasket' => json_encode([
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ]),
            'deliveryMethod' => 'inpost',
        ]);

        $response->assertRedirect('login');
    }

    public function test_orderPayment_not_a_owner_cant_access(): void
    {
        $category = Category::factory()->create([
            'slug' => 'milk',
        ]);
        $p1 = Product::factory()->create([
            'price' => 10.10,
            'category_id' => $category->id,
            'title' => 'title1',
        ]);
        $p2  = Product::factory()->create([
            'price' => 100.20,
            'category_id' => $category->id,
        ]);
        $p3  = Product::factory()->create([
            'price' => 1000.30,
            'category_id' => $category->id,
        ]);

        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $order = Order::create([
            'price' => 1231.00,
            'delivery_method' => 'inpost',
            'user_id' => $user->id,
        ]);
        $order->products()->attach([
            $p1->id => ['num' => 3],
            $p2->id => ['num' => 2],
            $p3->id => ['num' => 1],
        ]);

        $response = $this->actingAs($user2)->get("/order/payment/$order->id", [
            'productsInBasket' => json_encode([
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ]),
            'deliveryMethod' => 'inpost',
        ]);

        $response->assertStatus(403);
    }
}
