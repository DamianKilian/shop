<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageAttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url' => 'pages/' . Str::random(10) . 'AYuOMCAU25blislVwHTTIgq2dFePO5Xd.jpg',
        ];
    }
}
