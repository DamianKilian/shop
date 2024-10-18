<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageFile>
 */
class PageFileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url' => 'pages/' . fake()->regexify('[A-Z]{5}[0-4]{3}') . 'AYuOMCAU25blislVwHTTIgq2dFePO5Xd.jpg',
        ];
    }
}
