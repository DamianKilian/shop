<?php

namespace Tests\Feature\AdminPanel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_are_displaying(): void
    {
        $category = Category::create([
            'parent_id' => null,
            'name' => 'Example testing category name',
            'position' => 1,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin-panel/categories');
        // dd($response);//mmmyyy
        $response->assertStatus(200);
        $response->assertViewHas('categories', function ($collection) use ($category) {
            return $collection->contains($category);
        });
        $response->assertSee('Example testing category name');
    }

    public function test_categories_are_displaying_empty_table(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin-panel/categories');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cant_access_categories_page(): void
    {
        $response = $this->get('/admin-panel/categories');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_saveCategories(): void
    {
        $user = User::factory()->create();
        $now = date('Y-m-d H:i:s');
        $categoriesInitial = [
            "main-menu" => [
                ["id" => 1, "name" => "p1"],
                ["id" => 5, "name" => "p2"],
                ["id" => 10, "name" => "p3"],
                ["id" => 13, "name" => "p4"],
                ["id" => 17, "name" => "p6", "deleted_at" => $now],
                ["id" => 21, "name" => "p7", "deleted_at" => $now],
            ],
            "1" => [
                ["id" => 2, "name" => "p1ch1"],
                ["id" => 3, "name" => "p1ch2"],
                ["id" => 4, "name" => "p1ch3"]
            ],
            "5" => [
                ["id" => 6, "name" => "p2ch1"],
                ["id" => 8, "name" => "p2ch2"],
                ["id" => 9, "name" => "p2ch3"]
            ],
            "6" => [
                ["id" => 7, "name" => "p2ch1ch1"]
            ],
            "10" => [
                ["id" => 11, "name" => "p3ch1"],
                ["id" => 12, "name" => "p3ch2"]
            ],
            "13" => [
                ["id" => 14, "name" => "p4ch1"],
                ["id" => 15, "name" => "p4ch2"],
                ["id" => 16, "name" => "p4ch3"]
            ],
            "17" => [
                ["id" => 18, "name" => "p6ch1", "deleted_at" => $now],
                ["id" => 19, "name" => "p6ch2", "deleted_at" => $now],
                ["id" => 20, "name" => "p6ch3", "deleted_at" => $now]
            ],
            "21" => [
                ["id" => 22, "name" => "p7ch1", "deleted_at" => $now],
            ],
        ];
        $id = [];
        foreach ($categoriesInitial["main-menu"] as $position => $parent) {
            $parent['position'] = $position;
            $categoryCreated = Category::create($parent);
            $id[$parent['id']] = $categoryCreated->id;
            $id = $this->addSubCategories($parent, $id, $categoriesInitial);
        }
        $categoriesToSave = [
            $id["1"] => [
                ["id" => $id['3'], "name" => "p1ch2"],
                ["id" => $id['2'], "name" => "p1ch1"],
                ["id" => "new_5376972801700572", "name" => "p1ch4", "new" => true],
                ["id" => $id['4'], "name" => "p1ch3"]
            ],
            $id["5"] => [
                ["id" => $id['6'], "name" => "p2ch1"],
                ["id" => $id['8'], "name" => "p2ch2"],
                ["id" => $id['9'], "name" => "p2ch3"]
            ],
            $id["6"] => [
                ["id" => $id['7'], "name" => "p2ch1ch1"]
            ],
            $id["10"] => [
                ["id" => $id['11'], "name" => "p3ch1"],
                ["id" => $id['12'], "name" => "p3ch2"]
            ],
            $id["13"] => [
                ["id" => $id['14'], "name" => "p4ch1"],
                ["id" => $id['15'], "name" => "p4ch2_name_changed"],
                ["id" => $id['16'], "name" => "p4ch3"]
            ],
            $id["17"] => [
                ["id" => $id["18"], "name" => "p6ch1", "deleted_at" => $now, "restore" => true],
                ["id" => $id["19"], "name" => "p6ch2", "deleted_at" => $now, "restore" => false],
                ["id" => $id["20"], "name" => "p6ch3", "deleted_at" => $now, "restore" => false]
            ],
            $id["21"] => [
                ["id" => $id["22"], "name" => "p7ch1", "deleted_at" => $now, "restore" => true],
            ],
            "main-menu" => [
                ["id" => $id['5'], "name" => "p2"],
                ["id" => $id['1'], "name" => "p1"],
                ["id" => $id['10'], "name" => "p3", "remove" => true],
                ["id" => "new_6287336174597907", "name" => "p5", "new" => true],
                ["id" => $id['13'], "name" => "p4"],
                ["id" => $id['17'], "name" => "p6", "deleted_at" => $now, "restore" => true],
                ["id" => $id['21'], "name" => "p7", "deleted_at" => $now],
            ],
            "new_6287336174597907" => []
        ];
        $response = $this->actingAs($user)->postJson('/admin-panel/save-categories', ['categories' => $categoriesToSave]);
        $categoriesExpectedTree = [
            "p2" => [
                "p2ch1" => [
                    "p2ch1ch1" => []
                ],
                "p2ch2" => [],
                "p2ch3" => [],
            ],
            "p1" => [
                "p1ch2" => [],
                "p1ch1" => [],
                "p1ch4" => [],
                "p1ch3" => [],
            ],
            "p3" => [
                "p3ch1" => [],
                "p3ch2" => []
            ],
            "p5" => [],
            "p4" => [
                "p4ch1" => [],
                "p4ch2_name_changed" => [],
                "p4ch3" => []
            ],
            "p6" => [
                "p6ch1" => [],
                "p6ch2" => [],
                "p6ch3" => [],
            ],
            "p7" => [
                "p7ch1" => [],
            ]
        ];

        $categories = $response->getData()->categories;
        $categoriesTree = [];
        $categoriesByName = [];
        foreach ($categories as $category) {
            $categoriesByName[$category->name] = $category;
            if (null === $category->parent_id) {
                $categoriesTree[$category->name] = $this->addChildren($category, $categories);
            }
        }

        $this->assertTrue(null !== $categoriesByName['p3']->deleted_at);
        $this->assertTrue(null !== $categoriesByName['p3ch1']->deleted_at);
        $this->assertTrue(null !== $categoriesByName['p3ch2']->deleted_at);
        $this->assertTrue(null === $categoriesByName['p6']->deleted_at);
        $this->assertTrue(null === $categoriesByName['p6ch1']->deleted_at);
        $this->assertTrue(null !== $categoriesByName['p6ch2']->deleted_at);
        $this->assertTrue(null !== $categoriesByName['p7ch1']->deleted_at);
        $this->assertTrue($categoriesTree === $categoriesExpectedTree);
    }

    public function test_saveCategories_all_new(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/admin-panel/save-categories', ['categories' => [
            "main-menu" => [
                ["name" => "p1", "id" => "new_7831638039189243", "new" => true,],
                ["name" => "p2", "id" => "new_03850895405535737", "new" => true,],
                ["name" => "p3", "id" => "new_8016902347158529", "new" => true,],
            ],
            "new_7831638039189243" => [
                ["name" => "p1ch1", "id" => "new_16271295854768575", "new" => true,],
                ["name" => "p1ch2", "id" => "new_6114767555203788", "new" => true,]
            ],
            "new_16271295854768575" => [
                ["name" => "p1ch1ch1", "id" => "new_22932103328267117", "new" => true,]
            ],
            "new_03850895405535737" => [
                ["name" => "p2ch1", "id" => "new_2885262168746463", "new" => true,]
            ]
        ]]);
        $categoriesExpectedTree = [
            "p1" => [
                "p1ch1" => [
                    "p1ch1ch1" => []
                ],
                "p1ch2" => []
            ],
            "p2" => [
                "p2ch1" => []
            ],
            "p3" => []
        ];

        $categories = $response->getData()->categories;
        $categoriesTree = [];
        foreach ($categories as $category) {
            if (null === $category->parent_id) {
                $categoriesTree[$category->name] = $this->addChildren($category, $categories);
            }
        }

        $this->assertTrue($categoriesTree === $categoriesExpectedTree);
    }

    public function test_saveCategories_validation_errors(): void
    {
        $user = User::factory()->create();
        $categoriesInitial = [
            "main-menu" => [
                ["id" => 1, "name" => "p1"],
                ["id" => 5, "name" => "p2"],
                ["id" => 10, "name" => "p3"],
            ],
            "1" => [
                ["id" => 2, "name" => "p1ch1"],
                ["id" => 3, "name" => "p1ch2"],
            ],
        ];
        $id = [];
        foreach ($categoriesInitial["main-menu"] as $position => $parent) {
            $parent['position'] = $position;
            $categoryCreated = Category::create($parent);
            $id[$parent['id']] = $categoryCreated->id;
            $id = $this->addSubCategories($parent, $id, $categoriesInitial);
        }
        $categoriesToSave = [
            "main-menu" => [
                ["id" => $id['1'], "name" => "p1"],
                ["id" => $id['5'], "name" => "p2"],
                ["id" => $id['10'], "name" => "p3"],
            ],
            $id["1"] => [
                ["id" => $id['2'], "name" => "p1ch1"],
                ["id" => $id['3'], "name" => "p1ch2"],
                ["id" => "new_5376972801700572", "name" => "p1ch2", "new" => true],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/admin-panel/save-categories', ['categories' => $categoriesToSave]);
        $failedValidation = $response->getData()->failedValidation;

        $this->assertTrue($failedValidation->{'categories.1.1'} && $failedValidation->{'categories.1.2'});
    }

    protected function addChildren($parentCategory, $categories)
    {
        $subCategories = [];
        foreach ($categories as $category) {
            if ($category->parent_id === $parentCategory->id) {
                $subCategories[$category->name] = $this->addChildren($category, $categories);
            }
        }
        return $subCategories;
    }

    protected function addSubCategories($parent, $id, $categoriesInitial)
    {
        $parent_id = $id[$parent['id']];
        $children = isset($categoriesInitial[$parent['id']]) ? $categoriesInitial[$parent['id']] : [];
        foreach ($children as $position => $child) {
            $child['position'] = $position;
            $child['parent_id'] = $parent_id;
            $categoryCreated = Category::create($child);
            $id[$child['id']] = $categoryCreated->id;
            $id = $this->addSubCategories($child, $id, $categoriesInitial);
        }
        return $id;
    }
}
