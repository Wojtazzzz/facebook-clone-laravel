<?php

declare(strict_types=1);

namespace Tests\Feature\Next;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.next.user', $this->user->id);
    }

    public function testResponseReturnProperlyUser(): void
    {
        $response = $this->getJson($this->route);
        $response->assertOk();
    }
}
