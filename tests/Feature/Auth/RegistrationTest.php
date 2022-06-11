<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    private string $registerPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->registerPath = route('api.auth.register');
    }

    public function testUserCanRegister()
    {
        $response = $this->postJson($this->registerPath);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }
}
