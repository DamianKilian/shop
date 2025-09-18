<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $r = rand(0, 9);
        return [
            'amount' => 0 !== $r ? rand(15, 500000) : rand(-500000, -15),
            'created_at' => fake()->dateTimeBetween('-10 week'),
        ];
    }
}
