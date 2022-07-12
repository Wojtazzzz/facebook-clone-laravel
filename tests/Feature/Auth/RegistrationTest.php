<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->route = route('api.auth.register');
    }

    public function testUserCanRegister(): void
    {
        $response = $this->postJson($this->route);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }
}
