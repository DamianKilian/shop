<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url' => config('my.images_folder') . '/' . Str::random(10) . 'AYuOMCAU25blislVwHTTIgq2dFePO5Xd.jpg',
            'url_thumbnail' => config('my.thumbnails_folder') . '/' . Str::random(10) . 'AYuOMCAU25blislVwHTTIgq2dFePO5Xd.jpg',
            'position' => fake()->unique()->numberBetween(0, 100000),
        ];
    }
}
