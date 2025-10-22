<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbTest extends TestCase
{
    use RefreshDatabase;

    public function test_breadcrumb_one_category(): void
    {
        Category::factory()->create();
        $category = Category::factory()->create([
            'name' => 'cat',
            'slug' => 'category-slug',
        ]);
        $product = Product::factory()->create([
            'title' => 'product',
            'category_id' => $category->id,
        ]);

        $responseCategory = $this->get(route('category', ['slug' => $category->slug]));
        $responseProduct = $this->get(route('product', ['slug' => $product->slug]));

        $responseCategory->assertSeeInOrder(
            [
                '<a href="' . route('home') . '">' . config('app.name') . '</a>',
                '<a>cat</a>',
            ],
            true
        );
        $responseProduct->assertSeeInOrder(
            [
                '<a href="' . route('home') . '">' . config('app.name') . '</a>',
                '<a href="' . route('category', ['slug' => 'category-slug']) . '">cat</a>',
                '<a>product</a>',
            ],
            true
        );
    }

    public function test_breadcrumb_many_category(): void
    {
        Category::factory()->create();
        $parentParentCat = Category::factory()->create([
            'name' => 'parentParentCat',
            'slug' => 'parentParent-slug',
        ]);
        $parentCat = Category::factory()->create([
            'name' => 'parentCat',
            'parent_id' => $parentParentCat->id,
            'slug' => 'parent-slug',
        ]);
        $cat = Category::factory()->create([
            'name' => 'cat',
            'parent_id' => $parentCat->id,
            'slug' => 'slug',
        ]);
        $product = Product::factory()->create([
            'title' => 'product',
            'category_id' => $cat->id,
        ]);

        $responseCategory = $this->get(route('category', ['slug' => $cat->slug]));
        $responseProduct = $this->get(route('product', ['slug' => $product->slug]));

        $responseCategory->assertSeeInOrder(
            [
                '<a href="' . route('home') . '">' . config('app.name') . '</a>',
                '<a href="' . route('category', ['slug' => 'parentParent-slug']) . '">parentParentCat</a>',
                '<a href="' . route('category', ['slug' => 'parent-slug']) . '">parentCat</a>',
                '<a>cat</a>',
            ],
            true
        );
        $responseProduct->assertSeeInOrder(
            [
                '<a href="' . route('home') . '">' . config('app.name') . '</a>',
                '<a href="' . route('category', ['slug' => 'parentParent-slug']) . '">parentParentCat</a>',
                '<a href="' . route('category', ['slug' => 'parent-slug']) . '">parentCat</a>',
                '<a href="' . route('category', ['slug' => 'slug']) . '">cat</a>',
                '<a>product</a>',
            ],
            true
        );
    }
}
