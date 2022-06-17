<?php

declare(strict_types=1);

namespace Tests\Feature\Next;

use App\Models\User;
use Tests\TestCase;

class UsersTest extends TestCase
{
    private string $usersRoute;

    private string $usersTable = 'users';
    private int $usersCount = 20;

    public function setUp(): void
    {
        parent::setUp();

        $this->usersRoute = route('api.next.users');
    }

    public function testResponseReturnProperlyUsers(): void
    {
        User::factory($this->usersCount)->create();

        $users = User::latest()->get('id');

        $response = $this->getJson($this->usersRoute);

        $this->assertDatabaseCount($this->usersTable, $this->usersCount);

        $response->assertOk()
            ->assertJsonCount($this->usersCount, 'users')
            ->assertJsonFragment([
                'users' => $users,
            ]);
    }
}
