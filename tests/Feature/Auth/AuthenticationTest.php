<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_authenticate()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertNoContent();
    }

    public function test_user_must_authenticate_with_properly_email()
    {
        User::factory()->create();
        
        $response = $this->post('/login', [
            'email' => 'not_email',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_user_can_not_authenticate_without_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email
        ]);

        $this->assertGuest();
    }

    public function test_user_can_not_authenticate_without_email()
    {
        User::factory()->create();

        $this->post('/login', [
            'password' => 'password'
        ]);

        $this->assertGuest();
    }

    public function test_user_can_not_authenticate_without_any_data()
    {
        User::factory()->create();

        $this->post('/login');

        $this->assertGuest();
    }

}
