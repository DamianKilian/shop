<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'order_priority' => fake()->randomNumber(4, false),
        ];
    }
}
