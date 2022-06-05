<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testUserCanAuthenticate()
    {
        $response = $this->postJson('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }

    public function testUserMustAuthenticateWithProperlyEmail()
    {
        $response = $this->postJson('/login', [
            'email' => 'not_email',
            'password' => 'password',
        ]);

        $response->assertJson([
            'message' => 'The email must be a valid email address.',
        ]);
        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithInvalidPassword()
    {
        $this->postJson('/login', [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithoutPassword()
    {
        $this->postJson('/login', [
            'email' => $this->user->email,
        ]);

        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithoutEmail()
    {
        $this->postJson('/login', [
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithoutAnyData()
    {
        $this->postJson('/login');
        $this->assertGuest();
    }
}
