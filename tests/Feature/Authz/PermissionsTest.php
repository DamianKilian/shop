<?php

namespace Tests\Feature;

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
        $user = User::factory()->make();
        $response = $this->actingAs($user)->get('/admin-panel/products');

        $response->assertStatus(403);
    }
}
