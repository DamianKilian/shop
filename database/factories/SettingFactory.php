<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'desc' => fake()->sentence(),
            'input_type' => ['text', 'select'][rand(0, 1)],
            'value' => fake()->word(),
            'default_value' => fake()->word(),
            'order_priority' => fake()->randomNumber(4, false),
        ];
    }
}
