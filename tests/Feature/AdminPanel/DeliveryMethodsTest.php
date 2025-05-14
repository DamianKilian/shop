<?php

namespace Tests\Feature\Account;

use App\Models\DeliveryMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryMethodsTest extends TestCase
{
    use RefreshDatabase;

    public function test_deliveryMethods(): void
    {
        $response = $this->actingAs(parent::getAdmin())->get("admin-panel/delivery-methods");

        $response->assertStatus(200);
    }

    public function test_deliveryMethods_not_a_user_cant_access(): void
    {
        $response = $this->get("admin-panel/delivery-methods");

        $response->assertRedirect(route('login'));
    }

    public function test_getDeliveryMethods(): void
    {
        DeliveryMethod::factory()->count(5)->create();

        $response = $this->actingAs(parent::getAdmin())->postJson('admin-panel/get-delivery-methods');

        $response->assertStatus(200);
        $this->assertEquals(5, count($response['deliveryMethods']));
    }

    public function test_deleteDeliveryMethods(): void
    {
        $deliveryMethod = DeliveryMethod::factory()->create();
        $deliveryMethod2 = DeliveryMethod::factory()->create();
        $deliveryMethod3 = DeliveryMethod::factory()->create();

        $this->actingAs(parent::getAdmin())->postJson('admin-panel/delete-delivery-methods', [
            'deliveryMethods' => [
                $deliveryMethod,
                $deliveryMethod3,
            ]
        ]);

        $this->assertSoftDeleted($deliveryMethod);
        $this->assertNotSoftDeleted($deliveryMethod2);
        $this->assertSoftDeleted($deliveryMethod3);
    }

    public function test_addDeliveryMethod(): void
    {
        $response = $this->actingAs(parent::getAdmin())->post("admin-panel/add-delivery-method", [
            "name" => 'name',
            "price" => '111.22',
            "active" => 'true',
            "description" => 'description',
        ]);

        $this->assertDatabaseHas('delivery_methods', [
            "name" => 'name',
            "price" => '111.22',
        ]);
        $response->assertSuccessful();
    }

    public function test_addDeliveryMethod_edit(): void
    {
        $deliveryMethod = DeliveryMethod::factory()->create([
            "name" => 'name',
        ]);

        $response = $this->actingAs(parent::getAdmin())->post("admin-panel/add-delivery-method", [
            "deliveryMethodId" => $deliveryMethod->id,
            "name" => 'name2',
            "price" => '111.22',
            "active" => 'true',
            "description" => 'description',
        ]);

        $this->assertDatabaseHas('delivery_methods', [
            "id" => $deliveryMethod->id,
            "name" => 'name2',
        ]);
        $response->assertSuccessful();
    }
}
