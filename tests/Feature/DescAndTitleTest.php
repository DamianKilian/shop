<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DescAndTitleTest extends TestCase
{
    use RefreshDatabase;

    public function test_desc(): void
    {
        $sett = Setting::whereIn('name', ['DESC_MAIN', 'DESC_CATEGORY', 'DESC_PRODUCT'])->get()->keyBy('name');
        $sett['DESC_MAIN']->value('{shopName}, niskie ceny');
        $sett['DESC_CATEGORY']->value('{shopName}, kup w {cat}, {parentCat}, {parentParentCat}, niskie ceny');
        $sett['DESC_PRODUCT']->value('{shopName}, kup {product} za {price} w {cat}, {parentCat}, niskie ceny');
        $sett['DESC_MAIN']->save();
        $sett['DESC_CATEGORY']->save();
        $sett['DESC_PRODUCT']->save();
        config(['app.name' => 'Shop']);
        Category::factory()->create();
        $category = Category::factory()->create([
            'name' => 'cat',
        ]);
        $product = Product::factory()->create([
            'title' => 'product',
            'category_id' => $category->id,
            'price' => '101.20',
        ]);

        $responseMain = $this->get(route('home'));
        $responseCategory = $this->get(route('category', ['slug' => $category->slug]));
        $responseProduct = $this->get(route('product', ['slug' => $product->slug]));

        $responseMain->assertSee('<meta name="description" content="Shop, niskie ceny">', false);
        $responseCategory->assertSee('<meta name="description" content="Shop, kup w cat, niskie ceny">', false);
        $responseProduct->assertSee('<meta name="description" content="Shop, kup product za 101,20zł w cat, niskie ceny">', false);
    }

    public function test_desc_parentParentCat(): void
    {
        $sett = Setting::whereIn('name', ['DESC_MAIN', 'DESC_CATEGORY', 'DESC_PRODUCT'])->get()->keyBy('name');
        $sett['DESC_MAIN']->value('{shopName}, niskie ceny');
        $sett['DESC_CATEGORY']->value('{shopName}, kup w {cat}, {parentCat}, {parentParentCat}, niskie ceny');
        $sett['DESC_PRODUCT']->value('{shopName}, kup {product} za {price} w {cat}, {parentCat}, niskie ceny');
        $sett['DESC_MAIN']->save();
        $sett['DESC_CATEGORY']->save();
        $sett['DESC_PRODUCT']->save();
        config(['app.name' => 'Shop']);
        Category::factory()->create();
        $parentParentCat = Category::factory()->create([
            'name' => 'parentParentCat',
        ]);
        $parentCat = Category::factory()->create([
            'name' => 'parentCat',
            'parent_id' => $parentParentCat->id,
        ]);
        $cat = Category::factory()->create([
            'name' => 'cat',
            'parent_id' => $parentCat->id,
        ]);
        $product = Product::factory()->create([
            'title' => 'product',
            'category_id' => $cat->id,
            'price' => '101.20',
        ]);

        $responseMain = $this->get(route('home'));
        $responseCategory = $this->get(route('category', ['slug' => $cat->slug]));
        $responseProduct = $this->get(route('product', ['slug' => $product->slug]));

        $responseMain->assertSee('<meta name="description" content="Shop, niskie ceny">', false);
        $responseCategory->assertSee('<meta name="description" content="Shop, kup w cat, parentCat, parentParentCat, niskie ceny">', false);
        $responseProduct->assertSee('<meta name="description" content="Shop, kup product za 101,20zł w cat, parentCat, niskie ceny">', false);
    }
}
