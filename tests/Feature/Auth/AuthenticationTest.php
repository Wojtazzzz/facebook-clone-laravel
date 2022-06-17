<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    private User $user;
    private string $loginRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->loginRoute = route('api.auth.login');
    }

    public function testUserCanAuthenticate(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }

    public function testUserMustAuthenticateWithProperlyEmail(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'email' => 'not_email',
            'password' => 'password',
        ]);

        $response->assertJson([
            'message' => 'The email must be a valid email address.',
        ]);
        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithInvalidPassword(): void
    {
        $this->postJson($this->loginRoute, [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithoutPassword(): void
    {
        $this->postJson($this->loginRoute, [
            'email' => $this->user->email,
        ]);

        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithoutEmail(): void
    {
        $this->postJson($this->loginRoute, [
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function testUserCanNotAuthenticateWithoutAnyData(): void
    {
        $this->postJson($this->loginRoute);
        $this->assertGuest();
    }
}
