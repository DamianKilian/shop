<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\DeliveryMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $deliveryMethod = DeliveryMethod::factory()->create();
        return [
            'session_Id' => fake()->regexify('[a-z]{100}'),
            'price' => fake()->randomFloat(2, 20, 4000),
            'delivery_method_id' => $deliveryMethod->id,
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'address_invoice_id' => Address::factory(),
            'delivery_price' => $deliveryMethod->price,
        ];
    }
}
