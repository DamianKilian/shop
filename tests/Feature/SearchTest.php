<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_category(): void
    {
        $category = Category::factory()->create([
            'slug' => 'milk',
        ]);
        Product::factory()->count(50)->create([
            'title' => 'In a carton',
            'category_id' => $category->id,
        ]);

        $response = $this->get('/category/milk?page=2');

        $response->assertStatus(200);
        $response->assertSee(__('In a carton'));
    }

    public function test_getProductsView(): void
    {
        $categoryParent = Category::factory()->create();
        $category = Category::factory()->create([
            'parent_id' => $categoryParent->id,
        ]);
        Product::factory()->create([
            'title' => 'In a carton parent',
            'category_id' => $categoryParent->id,
        ]);
        Product::factory()->create([
            'title' => 'In a carton',
            'category_id' => $category->id,
        ]);
        $categories = CategoryService::getCategories();
        $categoryChildrenIds = CategoryService::getCategoryChildrenIds([$categoryParent->id], $categories);

        $response = $this->postJson('/api/get-products-view?page=1', [
            'categoryChildrenIds' => json_encode($categoryChildrenIds)
        ]);

        $response->assertStatus(200);
        $response->assertSee('In a carton');
        $response->assertSee('In a carton parent');
    }
}
