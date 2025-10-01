<?php

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user'));

        $response->assertStatus(200);
    }

    public function test_account_user_unauth(): void
    {
        $response = $this->get(route('user'));

        $response->assertStatus(302);
    }

    public function test_account_updateUser(): void
    {
        $user = User::factory()->create([
            'name' => 'name',
            'email' => 'email@email.com',
        ]);

        $response = $this->actingAs($user)->post(route('update-user'), [
            'name' => 'name1',
            'email' => 'email1@email.com',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'name1',
            'email' => 'email1@email.com',
        ]);
    }

    public function test_account_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('password'));

        $response->assertStatus(200);
    }

    public function test_account_updatePassword(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $r = $this->actingAs($user)->post(route('update-password'), [
            'current_password' => 'password',
            'password' => 'passwordNew',
            'password_confirmation' => 'passwordNew',
        ]);
        $incorrectCurrentPasswordRequest = $this->actingAs($user)->post(route('update-password'), [
            'current_password' => 'incorrectCurrentPassword',
            'password' => 'passwordNew',
            'password_confirmation' => 'passwordNew',
        ]);
        $user = User::find($user->id);
        $passwordNewHash = $user->password;

        $incorrectCurrentPasswordRequest->assertStatus(422);
        assertTrue(Hash::check('passwordNew', $passwordNewHash));
    }
}
