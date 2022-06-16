<?php

namespace Tests\Feature\Next;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user;

    private string $userRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->userRoute = route('api.next.user', $this->user->id);
    }

    public function testResponseReturnProperlyUser()
    {
        $response = $this->getJson($this->userRoute);
        $response->assertOk();
    }
}
