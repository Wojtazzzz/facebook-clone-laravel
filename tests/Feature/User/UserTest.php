<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Http\Resources\UserResource;
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
        $this->route = route('api.users.show');
    }

    public function testCanNotUserGetOwnDataAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUserGetOwnDataAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk();
    }

    public function testRequestReturnProperlyData(): void
    {
        $resource = new UserResource($this->user);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertJson($resource->response()->getData(true));
    }
}
