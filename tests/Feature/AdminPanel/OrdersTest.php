<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders(): void
    {
        $response = $this->actingAs(parent::getAdmin())->get("admin-panel/orders");

        $response->assertStatus(200);
    }

    public function test_orders_not_an_admin_cant_access(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("admin-panel/orders");

        $response->assertStatus(403);
    }

    public function test_orders_not_a_user_cant_access(): void
    {
        $response = $this->get("admin-panel/orders");

        $response->assertRedirect(route('login'));
    }

    public function test_getOrders(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        Order::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);
        Order::factory()->count(5)->create([
            'user_id' => $user2->id,
        ]);
        Order::factory()->count(5)->create();

        $response = $this->actingAs(parent::getAdmin())->post("admin-panel/get-orders");

        assertEquals(15, count($response['orders']['data']));
        $response->assertStatus(200);
    }

    public function test_getOrderData(): void
    {
        $response = $this->post("api/admin-panel/get-order-data");

        assertTrue(0 < count($response['orderStatuses']));
        $response->assertSuccessful();
    }

    public function test_addOrder(): void
    {
        $orderStatus = OrderStatus::first();

        $response = $this->actingAs(parent::getAdmin())->post("admin-panel/add-order", [
            "price" => '111,11',
            "orderStatusId" => $orderStatus->id,
        ]);

        $this->assertDatabaseHas('orders', [
            "price" => 111.11,
            "order_status_id" => $orderStatus->id,
            "created_in_admin_panel" => true,
        ]);
        $response->assertSuccessful();
    }

    public function test_addOrder_edit(): void
    {
        $orderStatus = OrderStatus::first();
        $orderStatus2 = OrderStatus::skip(1)->first();
        $order = Order::factory([
            "price" => 111.11,
            "order_status_id" => $orderStatus->id,
        ])->create();

        $response = $this->actingAs(parent::getAdmin())->post("admin-panel/add-order", [
            "orderId" => $order->id,
            "price" => '222,22',
            "orderStatusId" => $orderStatus2->id,
        ]);

        $this->assertDatabaseHas('orders', [
            "id" => $order->id,
            "price" => 222.22,
            "order_status_id" => $orderStatus2->id,
        ]);
        $response->assertSuccessful();
    }
}
