<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url' => 'attachments/' . Str::random(10) . 'AYuOMCAU25blislVwHTTIgq2dFePO5Xd.jpg',
            'hash' => '68b1282b91de2c054c36629cb8dd447f12f096d3e3c587978dc2248444633483',
        ];
    }
}
