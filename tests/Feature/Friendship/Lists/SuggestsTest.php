<?php

declare(strict_types=1);

namespace Tests\Feature\Friendship\Lists;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class SuggestsTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.friendship.suggests');
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseWhenAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk();
    }

    public function testNotFetchLoggedUser(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(0);
    }

    public function testNotFetchUserFriends(): void
    {
        User::factory()->createOne();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Friendship::factory(2)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(1);
    }

    public function testNotFetchUsersWhereRequestIsPending(): void
    {
        User::factory(3)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        Friendship::factory(2)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(3);
    }

    public function testNotFetchBlockedUsers(): void
    {
        User::factory(8)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        Friendship::factory(2)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(8);
    }

    public function testReturnMaxTenSuggests(): void
    {
        User::factory(18)->create();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(10);
    }

    public function testCanFetchMoreSuggestsFromSecondPage(): void
    {
        User::factory(17)->create();

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()->assertJsonCount(7);
    }
}
