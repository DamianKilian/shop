<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        User::factory()->create([
            'name' => 'test',
            'email' => 'test@example.com',
        ]);

        $cNum0 = 3;
        do {
            $category0 = $this->generate($cNum0--);
            $cNum1 = rand(0, 2);
            do {
                $category1 = $this->generate($cNum1--, $category0->id);
                $cNum2 = rand(0, 2);
                do {
                    $category2 = $this->generate($cNum2--, $category1->id);
                    $cNum3 = rand(0, 2);
                    do {
                        $category3 = $this->generate($cNum3--, $category2->id);
                    } while ($cNum3 >= 0);
                } while ($cNum2 >= 0);
            } while ($cNum1 >= 0);
        } while ($cNum0 >= 0);
    }

    protected function generate($position, $parentId = null)
    {
        $category = Category::factory()->create([
            'position' => $position,
            'parent_id' => $parentId,
        ]);
        Product::factory(rand(0, 49))->create([
            'category_id' => $category->id,
        ])->each(function ($p) {
            ProductPhoto::factory(rand(0, 2))->create([
                'product_id' => $p->id,
            ]);
        });
        return $category;
    }
}
