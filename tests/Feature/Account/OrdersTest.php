<?php

namespace Tests\Feature\Account;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_acc(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/account/orders");

        $response->assertStatus(200);
    }

    public function test_orders_acc_not_a_user_cant_access(): void
    {
        $response = $this->get("/account/orders");

        $response->assertRedirect(route('login'));
    }

    public function test_getOrders_acc(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        Order::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);
        Order::factory()->count(5)->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user)->post("/account/get-orders");

        assertEquals(5, count($response['orders']['data']));
        $response->assertStatus(200);
    }
}
