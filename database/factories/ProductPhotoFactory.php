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
        return [
            'url' => fake()->regexify('[A-Za-z0-9]{20}'),
            'url_small' => fake()->regexify('[A-Za-z0-9]{20}'),
            'position'   => function () {
                $max = ProductPhoto::max('position');
                return $max ?: 0;
            },
            'size' => fake()->numberBetween(0, 1000),
        ];
    }
}
