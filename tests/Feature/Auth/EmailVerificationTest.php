<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    // use RefreshDatabase;

    // protected $verificationVerifyRouteName = 'verification.verify';

    // protected function successfulVerificationRoute()
    // {
    //     return route('home');
    // }

    // protected function verificationNoticeRoute()
    // {
    //     return route('verification.notice');
    // }

    // protected function validVerificationVerifyRoute($user)
    // {
    //     return URL::signedRoute($this->verificationVerifyRouteName, [
    //         'id' => $user->id,
    //         'hash' => sha1($user->getEmailForVerification()),
    //     ]);
    // }

    // protected function invalidVerificationVerifyRoute($user)
    // {
    //     return route($this->verificationVerifyRouteName, [
    //         'id' => $user->id,
    //         'hash' => 'invalid-hash',
    //     ]);
    // }

    // protected function verificationResendRoute()
    // {
    //     return route('verification.resend');
    // }

    // protected function loginRoute()
    // {
    //     return route('login');
    // }

    // public function testGuestCannotSeeTheVerificationNotice()
    // {
    //     $response = $this->get($this->verificationNoticeRoute());

    //     $response->assertRedirect($this->loginRoute());
    // }

    // public function testUserSeesTheVerificationNoticeWhenNotVerified()
    // {
    //     $user = User::factory()->create([
    //         'email_verified_at' => null,
    //     ]);

    //     $response = $this->actingAs($user)->get($this->verificationNoticeRoute());

    //     $response->assertStatus(200);
    //     $response->assertViewIs('auth.verify');
    // }

    // public function testVerifiedUserIsRedirectedHomeWhenVisitingVerificationNoticeRoute()
    // {
    //     $user = User::factory()->create([
    //         'email_verified_at' => now(),
    //     ]);

    //     $response = $this->actingAs($user)->get($this->verificationNoticeRoute());

    //     $response->assertRedirect($this->successfulVerificationRoute());
    // }

    // public function testGuestCannotSeeTheVerificationVerifyRoute()
    // {
    //     $user = User::factory()->create([
    //         'id' => 1,
    //         'email_verified_at' => null,
    //     ]);

    //     $response = $this->get($this->validVerificationVerifyRoute($user));

    //     $response->assertRedirect($this->loginRoute());
    // }

    // public function testUserCannotVerifyOthers()
    // {
    //     $user = User::factory()->create([
    //         'id' => 1,
    //         'email_verified_at' => null,
    //     ]);

    //     $user2 = User::factory()->create(['id' => 2, 'email_verified_at' => null]);

    //     $response = $this->actingAs($user)->get($this->validVerificationVerifyRoute($user2));

    //     $response->assertForbidden();
    //     $this->assertFalse($user2->fresh()->hasVerifiedEmail());
    // }

    // public function testUserIsRedirectedToCorrectRouteWhenAlreadyVerified()
    // {
    //     $user = User::factory()->create([
    //         'email_verified_at' => now(),
    //     ]);

    //     $response = $this->actingAs($user)->get($this->validVerificationVerifyRoute($user));

    //     $response->assertRedirect($this->successfulVerificationRoute());
    // }

    // public function testForbiddenIsReturnedWhenSignatureIsInvalidInVerificationVerifyRoute()
    // {
    //     $user = User::factory()->create([
    //         'email_verified_at' => now(),
    //     ]);

    //     $response = $this->actingAs($user)->get($this->invalidVerificationVerifyRoute($user));

    //     $response->assertStatus(403);
    // }

    // public function testUserCanVerifyThemselves()
    // {
    //     $user = User::factory()->create([
    //         'email_verified_at' => null,
    //     ]);

    //     $response = $this->actingAs($user)->get($this->validVerificationVerifyRoute($user));

    //     $response->assertRedirect($this->successfulVerificationRoute());
    //     $this->assertNotNull($user->fresh()->email_verified_at);
    // }

    // public function testGuestCannotResendAVerificationEmail()
    // {
    //     $response = $this->post($this->verificationResendRoute());

    //     $response->assertRedirect($this->loginRoute());
    // }

    // public function testUserIsRedirectedToCorrectRouteIfAlreadyVerified()
    // {
    //     $user = User::factory()->create([
    //         'email_verified_at' => now(),
    //     ]);

    //     $response = $this->actingAs($user)->post($this->verificationResendRoute());

    //     $response->assertRedirect($this->successfulVerificationRoute());
    // }

    // public function testUserCanResendAVerificationEmail()
    // {
    //     Notification::fake();
    //     $user = User::factory()->create([
    //         'email_verified_at' => null,
    //     ]);

    //     $response = $this->actingAs($user)
    //         ->from($this->verificationNoticeRoute())
    //         ->post($this->verificationResendRoute());

    //     Notification::assertSentTo($user, VerifyEmail::class);
    //     $response->assertRedirect($this->verificationNoticeRoute());
    // }
}
