<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function testUserCanRegister()
    {
        $response = $this->postJson('/register');

        $response->assertNoContent();
        $this->assertAuthenticated();
    }
}
