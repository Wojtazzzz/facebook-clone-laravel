<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_user_can_register()
    {
        $response = $this->postJson('/register');

        $this->assertAuthenticated();
        $response->assertNoContent();
    }
}
