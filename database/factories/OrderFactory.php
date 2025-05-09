<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use App\Services\AppService;
use App\Services\DeliveryMethodsService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $deliveryMethodsService = new DeliveryMethodsService();
        $deliveryMethod = 'inpost';
        return [
            'session_Id' => fake()->regexify('[a-z]{100}'),
            'price' => fake()->randomFloat(2, 20, 4000),
            'delivery_method' => $deliveryMethod,
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'address_invoice_id' => Address::factory(),
            'delivery_price' => (int)AppService::toPennies($deliveryMethodsService->getDeliveryPrice($deliveryMethod)),
        ];
    }
}
