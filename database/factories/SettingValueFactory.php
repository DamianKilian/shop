<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingValueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'value' => fake()->word(),
            'desc' => fake()->sentence(),
            'order_priority' => fake()->randomNumber(4, false),
        ];
    }
}
