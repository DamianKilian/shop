<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_panel()
    {
        $response = $this->actingAs(parent::getAdmin())->get('/admin-panel/products');

        $response->assertSuccessful();
    }

    public function test_not_admin_can_not_access_admin_panel()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin-panel/products');

        $response->assertStatus(403);
    }

    public function test_usersManagement_can_access()
    {
        $user = User::factory()->create();
        $usersManagementPermission = Permission::whereName('usersManagement')->first();
        $adminPermission = Permission::whereName('admin')->first();
        $user->permissions()->attach([$usersManagementPermission->id, $adminPermission->id]);

        $response = $this->actingAs($user)->get('/admin-panel/users');

        $response->assertSuccessful();
    }
    public function test_usersManagement_can_not_access()
    {
        $user = User::factory()->create();
        $adminPermission = Permission::whereName('admin')->first();
        $user->permissions()->attach($adminPermission->id);

        $response = $this->actingAs($user)->get('/admin-panel/users');

        $response->assertStatus(403);
    }
}
