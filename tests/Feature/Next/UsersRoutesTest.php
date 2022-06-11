<?php

namespace Tests\Feature\Next;

use App\Http\Resources\UserResource;
use App\Models\User;
use Tests\TestCase;

class UsersRoutesTest extends TestCase
{
    private User $user;

    private string $usersRoute;
    private string $userRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->usersRoute = route('api.next.users');
        $this->userRoute = route('api.next.user', $this->user->id);
    }

    public function testResponseReturnProperlyUsers()
    {
        $usersCount = 20;

        User::factory($usersCount)->create();
        $users = User::latest()->get('id');

        $response = $this->getJson($this->usersRoute);

        $this->assertDatabaseCount(User::class, $usersCount + 1);

        $response->assertStatus(200)->assertJsonFragment([
            'users' => $users,
        ]);
    }

    public function testResponseReturnProperlyUser()
    {
        $resource = new UserResource($this->user);

        $response = $this->getJson($this->userRoute);

        $response->assertStatus(200)->assertJsonFragment($resource->response()->getData(true));
    }
}
