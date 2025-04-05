<?php

namespace Tests\Feature\Account;

use App\Models\Address;
use App\Models\AreaCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class AddressesTest extends TestCase
{
    use RefreshDatabase;

    public function test_addresses(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/account/addresses");

        $response->assertStatus(200);
    }

    public function test_addresses_not_a_user_cant_access(): void
    {
        $response = $this->get("/account/addresses");

        $response->assertRedirect(route('login'));
    }

    public function test_getAreaCodes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/account/get-area-codes");

        assertTrue('48' === $response['areaCodes'][0]['code']);
        assertTrue('48' === $response['defaultAreaCode']['code']);
        $response->assertStatus(200);
    }

    public function test_getAddresses(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $areaCode = AreaCode::whereCode('48')->first();

        Address::factory()->count(5)->create([
            'area_code_id' => $areaCode->id,
            'user_id' => $user->id,
        ]);
        Address::factory()->count(5)->create([
            'area_code_id' => $areaCode->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user)->post("/account/get-addresses");

        assertEquals(5, count($response['addresses']));
        $response->assertStatus(200);
    }

    public function test_deleteAddresses(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $addresses = Address::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        Address::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        Address::factory()->count(3)->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user)->post("/account/delete-addresses", [
            'addresses' => $addresses->toArray()
        ]);
        $softDeleted = Address::onlyTrashed()->get();

        $this->assertDatabaseCount('addresses', 9);
        assertEquals(3, count($softDeleted));
        $response->assertStatus(200);
    }

    public function test_addAddress(): void
    {
        $areaCode = AreaCode::whereCode('48')->first();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/account/add-address", [
            "email" => 'example@example.com',
            "name" => 'Name',
            "surname" => 'Surname',
            "phone" => '111222333',
            "nip" => '1112223334',
            "company_name" => 'company',
            "street" => 'street',
            "house_number" => '123b',
            "apartment_number" => '123',
            "postal_code" => '22-222',
            "city" => 'City',
            "area_code_id" => $areaCode->id,
        ]);

        $this->assertDatabaseHas('addresses', [
            "email" => 'example@example.com',
            "user_id" => $user->id,
        ]);
        $response->assertSuccessful();
    }

    public function test_addAddress_edit(): void
    {
        $areaCode = AreaCode::whereCode('48')->first();
        $user = User::factory()->create();
        $address = Address::factory()->create([
            "email" => 'example@example.com',
            "user_id" => $user->id,
        ]);

        $response = $this->actingAs($user)->post("/account/add-address", [
            "email" => 'example@example.com2',
            "name" => 'Name',
            "surname" => 'Surname',
            "phone" => '111222333',
            "nip" => '1112223334',
            "company_name" => 'company',
            "street" => 'street',
            "house_number" => '123b',
            "apartment_number" => '123',
            "postal_code" => '22-222',
            "city" => 'City',
            "area_code_id" => $areaCode->id,
            "addressId" => $address->id,
        ]);

        $this->assertDatabaseHas('addresses', [
            "id" => $address->id,
            "email" => 'example@example.com2',
            "user_id" => $user->id,
        ]);
        $response->assertSuccessful();
    }
}
