<?php

namespace Tests\Feature\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testCanNotUserGetOwnDataAsUnauthorized()
    {
        $response = $this->getJson('/api/user');
        $response->assertUnauthorized();
    }

    public function testCanUserGetOwnDataAsAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson('/api/user');
        $response->assertOk();
    }

    public function testRequestReturnProperlyData()
    {
        $resource = new UserResource($this->user);

        $response = $this->actingAs($this->user)->getJson('/api/user');

        $response->assertJson($resource->response()->getData(true));
    }
}
