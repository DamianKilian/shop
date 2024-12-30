<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainTest extends TestCase
{
    use RefreshDatabase;

    public function test_category(): void
    {
        $slug = 'slug';
        Category::factory()->create([
            'slug' => $slug,
        ]);

        $response = $this->get("/category/$slug");

        $response->assertStatus(200);
    }


    public function test_getProductsView(): void
    {
        $response = $this->post('/api/get-products-view');

        $response->assertStatus(200);
    }

    public function test_getProductsViewAllCategories(): void
    {
        $response = $this->post('/api/get-products-view-all-categories');

        $response->assertStatus(200);
    }

    public function test_getProductNums(): void
    {
        $response = $this->post('/api/get-product-nums');

        $response->assertStatus(200);
    }

    public function test_index(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_product(): void
    {
        $slug = 'slug';
        $category = Category::factory()->create();
        Product::factory()->create([
            'slug' => $slug,
            'category_id' => $category->id,
        ]);

        $response = $this->get("/product/$slug");

        $response->assertStatus(200);
    }
}
