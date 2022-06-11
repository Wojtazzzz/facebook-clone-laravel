<?php

namespace Tests\Feature\User;

use App\Http\Resources\UserResource;
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
        $this->userRoute = route('api.user');
    }

    public function testCanNotUserGetOwnDataAsUnauthorized()
    {
        $response = $this->getJson($this->userRoute);
        $response->assertUnauthorized();
    }

    public function testCanUserGetOwnDataAsAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson($this->userRoute);
        $response->assertOk();
    }

    public function testRequestReturnProperlyData()
    {
        $resource = new UserResource($this->user);

        $response = $this->actingAs($this->user)->getJson($this->userRoute);

        $response->assertJson($resource->response()->getData(true));
    }
}
