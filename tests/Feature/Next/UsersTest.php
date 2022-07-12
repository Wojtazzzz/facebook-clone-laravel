<?php

declare(strict_types=1);

namespace Tests\Feature\Next;

use App\Models\User;
use Tests\TestCase;

class UsersTest extends TestCase
{
    private string $route;
    private string $table = 'users';

    public function setUp(): void
    {
        parent::setUp();

        $this->route = route('api.next.users');
    }

    public function testResponseReturnProperlyUsers(): void
    {
        User::factory(16)->create();

        $response = $this->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(16, 'users');

        $this->assertDatabaseCount($this->table, 16);
    }
}
