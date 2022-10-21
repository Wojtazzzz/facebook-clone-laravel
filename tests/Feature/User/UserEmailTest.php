<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\User;
use Tests\TestCase;

class UserEmailTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    private function getRoute(): string
    {
        return route('api.users.email.index');
    }

    public function testCannotUseAsUnathorized(): void
    {
        $response = $this->getJson($this->getRoute());
        $response->assertUnauthorized();
    }

    public function testReturnCorrectEmail(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->getRoute());

        $response->assertOk()
            ->assertExactJson([$this->user->email]);
    }
}
