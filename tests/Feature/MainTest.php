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

    public function test_index_when_product_is_inactive(): void
    {
        $active = false;
        $slug = 'slug';
        $category = Category::factory()->create();
        Product::factory()->create([
            'active' => $active,
            'slug' => $slug,
            'category_id' => $category->id,
        ]);

        $response = $this->get("/$slug");

        $response->assertStatus(404);
    }

    public function test_index_when_PREVIEW_SLUG_and_user_unauth(): void
    {
        $slug = env('PREVIEW_SLUG');
        $category = Category::factory()->create();
        Product::factory()->create([
            'slug' => $slug,
            'category_id' => $category->id,
        ]);

        $response = $this->get("/$slug");

        $response->assertRedirect('/login');
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
