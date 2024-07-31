<?php

namespace Database\Factories;

use App\Models\ProductPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPhoto>
 */
class ProductPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $num = rand(1, 1000);
        return [
            'url' => "products/XUQ61zwxYhCcSBqs0IPeJm7rTY19n8cTCFy5rV3F$num.jpg",
            'url_small' => "products/small/XUQ61zwxYhCcSBqs0IPeJm7rTY19n8cTCFy5rV3F$num.jpg",
            'position' => fake()->unique()->numberBetween(0, 100000),
            'size' => fake()->numberBetween(0, 1000),
        ];
    }
}
