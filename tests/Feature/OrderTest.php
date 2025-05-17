<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\DeliveryMethod;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_orderCompleted(): void
    {
        $order = Order::factory()->create();
        $response = $this->get('order/completed/' . $order->id);

        $response->assertSuccessful();
    }

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
        $deliveryMethod = DeliveryMethod::factory([
            'name' => 'InPost',
            'price' => 100,
        ])->create();
        $addressData = $this->getAddressData();
        $addressDataInvoice = $addressData;
        $addressDataInvoice['email'] = "adsa@dad.asdaInvoice";
        $response = $this->post("/order/store", [
            'productsInBasket' => json_encode([
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ]),
            'deliveryMethodId' => $deliveryMethod->id,
            "addressInvoiceTheSame" => "false",
            "address" => $addressData,
            "addressInvoice" => $addressDataInvoice,
        ]);
        $order = Order::with('products')->latest()->first();
        $address = Address::where('email', 'adsa@dad.asd')->first();
        $addressInvoice = Address::where('email', 'adsa@dad.asdaInvoice')->first();

        assertTrue($order->address_id === $address->id);
        assertTrue($order->address_invoice_id === $addressInvoice->id);
        $this->assertDatabaseHas('addresses', [
            'email' => 'adsa@dad.asdaInvoice',
        ]);
        assertTrue(3 === $order->products()->count());
        $response->assertSessionHas('summary', function ($value) {
            return $value['formatted'] == [
                "productsPrice" => "1 231,00",
                "deliveryPrice" => "100,00",
                "totalPrice" => "1 331,00",
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
            $dmArr = json_decode($value);
            return 'InPost' == $dmArr->name;
        });
        $response->assertSessionHas('orderId', function ($value) use ($order) {
            return $value == $order->id;
        });
        $response->assertRedirect();
    }

    protected function getAddressData($name = 'Jan')
    {
        return [
            "email" => "adsa@dad.asd",
            "name" => $name,
            "surname" => "Surname",
            "nip" => "123123",
            "company_name" => "company",
            "phone" => "111222333",
            "street" => "street",
            "house_number" => "123",
            "apartment_number" => "123",
            "postal_code" => "22-222",
            "city" => "Rzeszów",
            "area_code_id" => "1",
            "country_id" => rand(1, 249),
        ];
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
        $deliveryMethod = DeliveryMethod::factory([
            'name' => 'InPost',
            'price' => 100,
        ])->create();

        $this->followingRedirects();
        $response = $this->post("/order/store", [
            'productsInBasket' => json_encode([
                $p1->id => ['num' => 3],
                $p2->id => ['num' => 2],
                $p3->id => ['num' => 1],
            ]),
            'deliveryMethodId' => $deliveryMethod->id,
            "addressInvoiceTheSame" => "false",
            "address" => $this->getAddressData(),
            "addressInvoice" => $this->getAddressData('Jan2'),
        ]);
        $order = Order::latest()->first(['id']);
        $productsInBasketData = json_decode($response['productsInBasketData'], true);
        $summary = json_decode($response['summary'], true);
        $deliveryMethod = json_decode($response['deliveryMethod'], true);
        $addresses = json_decode($response['addresses']);

        $response->assertStatus(200);
        assertTrue($addresses->address->id !== $addresses->addressInvoice->id);
        assertTrue('title1' === $productsInBasketData[$p1->id]['title']);
        assertTrue('1 331,00' === $summary['totalPrice']);
        assertTrue('InPost' === $deliveryMethod['name']);
        assertTrue($order->id === session()->get('orderId'));
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
        $user = User::factory()->create();
        $deliveryMethod = DeliveryMethod::factory([
            'name' => 'InPost',
            'price' => 100,
        ])->create();
        $order = Order::factory()->create([
            'price' => 1231.00,
            'delivery_method_id' => $deliveryMethod->id,
            'user_id' => $user->id,
        ]);
        $order->products()->attach([
            $p1->id => ['num' => 3],
            $p2->id => ['num' => 2],
            $p3->id => ['num' => 1],
        ]);

        $response = $this->actingAs($user)->get("/order/payment/$order->id");
        $productsInBasketData = json_decode($response['productsInBasketData'], true);
        $summary = json_decode($response['summary'], true);
        $deliveryMethod = json_decode($response['deliveryMethod'], true);
        $addresses = json_decode($response['addresses']);

        $response->assertStatus(200);
        assertTrue($addresses->address->id !== $addresses->addressInvoice->id);
        assertTrue('title1' === $productsInBasketData[$p1->id]['title']);
        assertTrue('1 331,00' === $summary['totalPrice']);
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
        $deliveryMethod = DeliveryMethod::factory([
            'name' => 'InPost',
            'price' => 100,
        ])->create();
        $order = Order::factory()->create([
            'price' => 1231.00,
            'delivery_method_id' => $deliveryMethod->id,
        ]);
        $order->products()->attach([
            $p1->id => ['num' => 3],
            $p2->id => ['num' => 2],
            $p3->id => ['num' => 1],
        ]);

        $response = $this->get("/order/payment/$order->id");

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

        $order = Order::factory()->create([
            'price' => 1231.00,
            'user_id' => $user->id,
        ]);
        $order->products()->attach([
            $p1->id => ['num' => 3],
            $p2->id => ['num' => 2],
            $p3->id => ['num' => 1],
        ]);

        $response = $this->actingAs($user2)->get("/order/payment/$order->id");

        $response->assertStatus(403);
    }
}
