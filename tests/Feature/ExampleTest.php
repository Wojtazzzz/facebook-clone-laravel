<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_not_found_response()
    {
        $response = $this->get('/');

        $response->assertStatus(404);
    }
}
