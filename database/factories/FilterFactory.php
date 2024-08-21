<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Filter>
 */
class FilterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst(fake()->words(2, true));
        $datetime = fake()->dateTimeBetween('-1 month', 'now');
        return [
            'name' => $name,
            'order_priority' => fake()->numberBetween(0, 100000),
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ];
    }
}
