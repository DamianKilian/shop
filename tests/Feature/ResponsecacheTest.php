<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Spatie\ResponseCache\Facades\ResponseCache;
use Tests\TestCase;

class ResponsecacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_home(): void
    {
        Config::set("responsecache.add_cache_time_header", true);
        ResponseCache::clear();
        $routeHome = route('home');

        $responseHome = $this->get($routeHome);
        $responseHome2 = $this->get($routeHome);

        $responseHome->assertStatus(200);
        $responseHome->assertHeaderMissing('laravel-responsecache');
        $responseHome2->assertHeader('laravel-responsecache');
    }

    public function test_category(): void
    {
        Config::set("responsecache.add_cache_time_header", true);
        ResponseCache::clear();
        $category = Category::factory()->create();
        $routeCategory = route('category', ['slug' => $category->slug]);

        $responseCategory = $this->get($routeCategory);
        $responseCategory2 = $this->get($routeCategory);

        $responseCategory->assertStatus(200);
        $responseCategory->assertHeaderMissing('laravel-responsecache');
        $responseCategory2->assertHeader('laravel-responsecache');
    }

    public function test_product(): void
    {
        Config::set("responsecache.add_cache_time_header", true);
        ResponseCache::clear();
        $product = Product::factory()->create();
        $routeProduct = route('product', ['slug' => $product->slug]);

        $responseProduct = $this->get($routeProduct);
        $responseProduct2 = $this->get($routeProduct);

        $responseProduct->assertStatus(200);
        $responseProduct->assertHeaderMissing('laravel-responsecache');
        $responseProduct2->assertHeader('laravel-responsecache');
    }

    public function test_clear_cache_on_model_change(): void
    {
        Config::set("responsecache.add_cache_time_header", true);
        ResponseCache::clear();
        $page = Page::factory()->create([
            'title' => 'title'
        ]);
        $category = Category::factory()->create([
            'name' => 'name',
        ]);
        $product = Product::factory()->create([
            'title' => 'title'
        ]);
        $routeHome = route('home');
        $routeCategory = route('category', ['slug' => $category->slug]);
        $routeProduct = route('product', ['slug' => $product->slug]);

        $this->get($routeHome);
        $this->get($routeCategory);
        $this->get($routeProduct);
        $page->title = 'title2';
        $page->save();
        $responseHome2 = $this->get($routeHome);
        $responseCategory2 = $this->get($routeCategory);
        $responseProduct2 = $this->get($routeProduct);
        $category->name = 'name2';
        $category->save();
        $responseHome3 = $this->get($routeHome);
        $responseCategory3 = $this->get($routeCategory);
        $responseProduct3 = $this->get($routeProduct);
        $product->title = 'title2';
        $product->save();
        $responseHome4 = $this->get($routeHome);
        $responseCategory4 = $this->get($routeCategory);
        $responseProduct4 = $this->get($routeProduct);

        $responseHome2->assertHeaderMissing('laravel-responsecache');
        $responseCategory2->assertHeaderMissing('laravel-responsecache');
        $responseProduct2->assertHeaderMissing('laravel-responsecache');
        $responseHome3->assertHeaderMissing('laravel-responsecache');
        $responseCategory3->assertHeaderMissing('laravel-responsecache');
        $responseProduct3->assertHeaderMissing('laravel-responsecache');
        $responseHome4->assertHeaderMissing('laravel-responsecache');
        $responseCategory4->assertHeaderMissing('laravel-responsecache');
        $responseProduct4->assertHeaderMissing('laravel-responsecache');
    }
}
