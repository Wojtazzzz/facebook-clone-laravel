<?php

namespace Tests\Feature\Next;

use App\Http\Resources\UserResource;
use App\Models\User;
use Tests\TestCase;

class UsersRoutesTest extends TestCase
{
    public function testResponseReturnProperlyUsers()
    {
        User::factory(20)->create();
        $users = User::latest()->get('id');

        $response = $this->getJson('/api/users');

        $this->assertDatabaseCount(User::class, 20);

        $response->assertStatus(200)->assertJsonFragment([
            'users' => $users,
        ]);
    }

    public function testResponseReturnProperlyUser()
    {
        $user = User::factory()->createOne();
        $resource = new UserResource($user);

        $response = $this->getJson("/api/users/$user->id");

        $response->assertStatus(200)->assertJsonFragment($resource->response()->getData(true));
    }
}
