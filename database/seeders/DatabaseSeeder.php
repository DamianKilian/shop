<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\File;
use App\Models\Filter;
use App\Models\FilterOption;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    protected $currFilterOptions;

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
            $this->currFilterOptions = collect();
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
        $that = $this;
        $category = Category::factory()->create([
            'position' => $position,
            'parent_id' => $parentId,
        ]);
        $filters = Filter::factory(rand(0, 2))->create()->each(function ($f) use ($that) {
            $filterOptions = FilterOption::factory(rand(1, 5))->create([
                'filter_id' => $f->id,
            ]);
            $that->currFilterOptions = $that->currFilterOptions->merge($filterOptions);
        });
        $category->filters()->attach($filters);
        Product::factory(rand(0, 49))->create([
            'category_id' => $category->id,
        ])->each(function ($p) use ($that) {
            // File::factory(rand(0, 2))->create([
            //     'product_id' => $p->id,
            // ]);
            $count = $that->currFilterOptions->count();
            $optionsNum = (int) ($count / rand(1, 9));
            if (0 < $optionsNum) {
                foreach ($that->currFilterOptions->random($optionsNum) as $filterOption) {
                    $p->filterOptions()->attach($filterOption);
                }
            }
        });
        return $category;
    }
}
