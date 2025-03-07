<?php

namespace Tests\Feature\AdminPanel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Filter;
use App\Models\FilterOption;

class FiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_are_displaying(): void
    {
        $categoryParent = Category::factory()->create([
            'name' => 'parent category',
        ]);
        Category::factory()->create([
            'name' => 'child category',
            'parent_id' => $categoryParent->id
        ]);

        $response = $this->actingAs(parent::getAdmin())->get('/admin-panel/filters');

        $response->assertStatus(200);
        $response->assertViewHas('categoryOptions', function ($categoryOptions) {
            $categoryOptionsArr = json_decode($categoryOptions);
            return 2 === count($categoryOptionsArr);
        });
        $response->assertSee('admin-panel-filters');
    }

    public function test_getFilters(): void
    {
        $filter = Filter::factory()->create();
        FilterOption::factory()->create([
            'filter_id' => $filter->id,
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-filters');

        $response->assertStatus(200);

        $this->assertTrue(1 === count($response['filters']['data']));
        $this->assertTrue(1 === count($response['filters']['data'][0]['filter_options']));
    }

    public function test_getFilters_byCategory(): void
    {
        $filter = Filter::factory()->create();
        FilterOption::factory()->create([
            'filter_id' => $filter->id,
        ]);
        $filter2 = Filter::factory()->create([
            'name' => 'filter2'
        ]);
        FilterOption::factory()->create([
            'filter_id' => $filter2->id,
        ]);
        $filter3 = Filter::factory()->create([
            'name' => 'filter3'
        ]);
        $category = Category::factory()->create();
        $category2 = Category::factory()->create([
            'parent_id' => $category->id
        ]);
        $category->filters()->save($filter2);
        $category2->filters()->save($filter3);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-filters', ['categoryId' => $category->id]);
        $response2 = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-filters', ['categoryId' => $category2->id]);

        $response->assertStatus(200);
        $response2->assertStatus(200);
        $this->assertTrue(2 === count($response['filters']['data']));
        $this->assertTrue(1 === count($response2['filters']['data']));
    }

    public function test_addFilter(): void
    {
        $this->actingAs(parent::getAdmin())->post('/admin-panel/add-filter', [
            'name' => 'filterName',
            'order_priority' => 123,
            'filterOptionIds' => [
                null
            ],
            'filterOptionNames' => [
                'oName'
            ],
            'filterOptionOrderPriorities' => [
                456
            ],
        ]);
        $this->assertDatabaseCount('filters', 1);
        $this->assertDatabaseCount('filter_options', 1);
    }

    public function test_addFilter_Edit(): void
    {
        Filter::factory()->count(3)->create();
        $filter = Filter::factory()->create([
            'name' => 'f1',
            'order_priority' => 1,
        ]);
        $filterOption = FilterOption::factory()->create([
            'name' => 'o1',
            'order_priority' => 1,
            'filter_id' => $filter->id,
        ]);
        $filterOption2 = FilterOption::factory()->create([
            'name' => 'oRemove',
            'order_priority' => 2,
            'filter_id' => $filter->id,
        ]);

        $this->actingAs(parent::getAdmin())->post('/admin-panel/add-filter', [
            'filterId' => $filter->id,
            'name' => 'f2',
            'order_priority' => 2,
            'filterOptionIds' => [
                $filterOption->id,
                null,
                $filterOption2->id,
            ],
            'filterOptionNames' => [
                'o2',
                'oNew',
                'oRemove',
            ],
            'filterOptionOrderPriorities' => [
                2,
                100,
                101,
            ],
            'filterOptionRemoves' => [
                null,
                null,
                'true',
            ],

        ]);

        $this->assertDatabaseHas('filters', [
            'id' => $filter->id,
            'name' => 'f2',
            'order_priority' => 2,
        ]);
        $this->assertDatabaseHas('filter_options', [
            'id' => $filterOption->id,
            'name' => 'o2',
            'order_priority' => 2,
        ]);
        $this->assertSoftDeleted($filterOption2);
        $this->assertDatabaseHas('filter_options', [
            'name' => 'oNew',
            'order_priority' => 100,
        ]);
    }

    public function test_deleteFilters(): void
    {
        $filter = Filter::factory()->create();
        $filter2 = Filter::factory()->create();
        $filter3 = Filter::factory()->create();

        $this->actingAs(parent::getAdmin())->postJson('/admin-panel/delete-filters', [
            'filters' => [
                ['id' => $filter->id,],
                ['id' => $filter2->id,],
            ]
        ]);

        $this->assertSoftDeleted($filter);
        $this->assertSoftDeleted($filter2);
        $this->assertNotSoftDeleted($filter3);
    }
}
