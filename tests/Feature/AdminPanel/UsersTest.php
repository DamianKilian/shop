<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings(): void
    {
        $response = $this->actingAs(parent::getAdmin())->get('/admin-panel/users');

        $response->assertStatus(200);
    }

    public function test_getUsers(): void
    {
        User::factory()->count(3)->create();
        $adminPermission = Permission::whereName('admin')->first();
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();
        $admin1->permissions()->attach($adminPermission);
        $admin2->permissions()->attach($adminPermission);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-users', []);

        $response->assertStatus(200);
        assertEquals(3, count($response['allUsers']['admins']));
        assertEquals(3, count($response['allUsers']['users']['data']));
    }

    public function test_setPermission(): void
    {
        User::factory()->count(3)->create();
        $user = User::factory()->create();
        $adminPermission = Permission::whereName('admin')->first();

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/set-permission', [
            'userId' => $user->id,
            'permission' => [
                'id' => $adminPermission->id,
                'checked' => true,
            ],
        ]);
        $count = User::withWhereHas('permissions', function ($query) {
            $query->whereName('admin');
        })->whereId($user->id)->count();

        $response->assertStatus(200);
        assertEquals(1, $count);
    }

    public function test_setPermission_unset(): void
    {
        User::factory()->count(3)->create();
        $adminPermission = Permission::whereName('admin')->first();
        $admin = User::factory()->create();
        $admin->permissions()->attach($adminPermission);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/set-permission', [
            'userId' => $admin->id,
            'permission' => [
                'id' => $adminPermission->id,
                'checked' => false,
            ],
        ]);
        $count = User::withWhereHas('permissions', function ($query) {
            $query->whereName('admin');
        })->whereId($admin->id)->count();

        $response->assertStatus(200);
        assertEquals(0, $count);
    }

    public function test_setPermission_acting_on_current_user(): void
    {
        User::factory()->count(3)->create();
        $usersManagementPermission = Permission::whereName('usersManagement')->first();
        $adminPermission = Permission::whereName('admin')->first();
        $admin = User::factory()->create();
        $admin->permissions()->attach([$usersManagementPermission->id, $adminPermission->id]);

        $response = $this->actingAs($admin)->postJson('/admin-panel/set-permission', [
            'userId' => $admin->id,
            'permission' => [
                'id' => $adminPermission->id,
                'checked' => false,
            ],
        ]);

        assertTrue('0' === $response->getContent());
    }

    public function test_searchUsers(): void
    {
        User::factory()->count(3)->create();
        $user = User::factory()->create([
            'name' => 'testName',
        ]);
        $user2 = User::factory()->create([
            'email' => 'testEmail',
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/search-users', [
            'searchUsersVal' => 'test',
        ]);

        $response->assertStatus(200);
        foreach ($response['users']['data'] as $value) {
            if ($user->id === $value['id']) {
                assertTrue($user->name === $value['name']);
            } else {
                assertTrue($user2->email === $value['email']);
            }
        }
    }
}
