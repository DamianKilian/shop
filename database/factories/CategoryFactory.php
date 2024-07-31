<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst(fake()->words(2, true));
        $slug = str_replace(" ", "-", $name) . fake()->unique()->randomNumber(5, false);
        $datetime = fake()->dateTimeBetween('-1 month', 'now');
        return [
            'name' => $name,
            'slug' => $slug,
            'position' => fake()->unique()->numberBetween(0, 100000),
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ];
    }
}
