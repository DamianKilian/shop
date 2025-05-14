<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(3),
            'active' => true,
            'price' => fake()->randomFloat(2, 20, 4000),
        ];
    }
}
