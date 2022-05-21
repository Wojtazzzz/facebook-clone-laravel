<?php

namespace Tests\Feature\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_can_not_user_get_own_data_as_unauthorized()
    {
        User::factory()->create();

        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
    }

    public function test_can_user_get_own_data_as_authorized()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertOk();
    }

    public function test_request_return_properly_data()
    {
        $user = User::factory()->createOne();
        $resource = new UserResource($user);

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertJson($resource->response()->getData(true));
    }
}
