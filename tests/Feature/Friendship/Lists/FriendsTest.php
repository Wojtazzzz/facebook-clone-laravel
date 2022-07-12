<?php

declare(strict_types=1);

namespace Tests\Feature\Friendship\Lists;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.friendship.friends', $this->user->id);
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

    public function testReturnFriendsInvitedAndWhichSendInvites(): void
    {
        Friendship::factory(4)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Friendship::factory(4)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(8);
    }

    public function testReturnFriendsWhenUserHasOnlyInvitedFriends(): void
    {
        Friendship::factory(9)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(9);
    }

    public function testReturnFriendsWhenUserHasOnlyFriendsWhichInvite(): void
    {
        Friendship::factory(4)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(4);
    }

    public function testMaxReturnTenFriends(): void
    {
        Friendship::factory(12)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(10);
    }

    public function testCanFetchMoreFriendsFromSecondPage(): void
    {
        Friendship::factory(16)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->route.'?page=2');

        $response->assertOk()->assertJsonCount(6);
    }
}
