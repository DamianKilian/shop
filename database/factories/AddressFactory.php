<?php

namespace Database\Factories;

use App\Models\AreaCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->word(),
            'surname' => fake()->word(),
            'nip' => fake()->numerify('##########'),
            'company_name' => fake()->word(),
            'phone' => fake()->numerify('#########'),
            'street' => fake()->word(),
            'house_number' => fake()->numerify('###') . fake()->randomLetter(),
            'apartment_number' => fake()->numerify('##'),
            'postal_code' => fake()->numerify('##-###'),
            'city' => fake()->word(),
            'area_code_id' => AreaCode::factory(),
            'country_id' => fake()->numberBetween(1, 249),
        ];
    }
}
