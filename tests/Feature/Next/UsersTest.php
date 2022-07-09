<?php

declare(strict_types=1);

namespace Tests\Feature\Next;

use App\Models\User;
use Tests\TestCase;

class UsersTest extends TestCase
{
    private string $usersRoute;

    private string $usersTable = 'users';

    public function setUp(): void
    {
        parent::setUp();

        $this->usersRoute = route('api.next.users');
    }

    public function testResponseReturnProperlyUsers(): void
    {
        User::factory(16)->create();

        $response = $this->getJson($this->usersRoute);
        $response->assertOk()
            ->assertJsonCount(16, 'users');

        $this->assertDatabaseCount($this->usersTable, 16);
    }
}
