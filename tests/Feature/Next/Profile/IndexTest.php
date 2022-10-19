<?php

declare(strict_types=1);

namespace Tests\Feature\Next\Profile;

use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->route = route('api.ssg.index');
    }

    public function testResponseReturnProperlyUsersData(): void
    {
        User::factory(16)->create();

        $user = User::first();

        $response = $this->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(16)
            ->assertJsonMissing([
                'first_name' => $user->first_name,
            ]);
    }
}
