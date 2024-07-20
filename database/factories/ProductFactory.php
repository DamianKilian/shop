<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $text = 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Explicabo amet corporis dolor itaque cupiditate id voluptatem fugit, autem, optio omnis unde libero accusamus quod perspiciatis. Obcaecati officiis odit minus voluptatum.';
        return [
            'title' => fake()->name(),
            'slug' => fake()->name(),
            'description' => '{"time":1719562614808,"blocks":[{"id":"tavms-lRPX","type":"paragraph","data":{"text":"' . $text . '"}},{"id":"SlAgNsoX6J","type":"list","data":{"style":"unordered","items":["11dsadasd","22asdasdasd","33asdasdasd<br>"]}}],"version":"2.29.1"}',
            'description_str' => $text,
            'price' => 0.55 + fake()->numberBetween(0, 100),
            'quantity' => fake()->numberBetween(0, 100),
        ];
    }
}
