<?php

declare(strict_types=1);

namespace Tests\Feature\UserFriends;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private User $friend;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
    }

    private function getRoute(int $count, User $user): string
    {
        return route('api.users.getByCount', [
            'user' => $user,
            'count' => $count,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->getRoute(1, $this->friend));
        $response->assertUnauthorized();
    }

    public function testCanGetUserFriends(): void
    {
        Friendship::factory(2)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute(2, $this->friend));

        $response->assertOk()
            ->assertJsonCount(2, 'friends')
            ->assertJsonFragment([
                'count' => 2,
            ]);
    }

    public function testCanGetOwnFriends(): void
    {
        Friendship::factory(3)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute(3, $this->user));

        $response->assertOk()
            ->assertJsonCount(3, 'friends')
            ->assertJsonFragment([
                'count' => 3,
            ]);
    }

    public function testReturnOnlySpecifedCountOfFriends(): void
    {
        Friendship::factory(12)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute(9, $this->friend));

        $response->assertOk()
            ->assertJsonCount(9, 'friends')
            ->assertJsonFragment([
                'count' => 12,
            ]);
    }

    public function testSpecifedCountOfFriendsCanBeHigherThanCurrentCountOfFriends(): void
    {
        Friendship::factory(3)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute(8, $this->friend));

        $response->assertOk()
            ->assertJsonCount(3, 'friends')
            ->assertJsonFragment([
                'count' => 3,
            ]);
    }

    public function testReturnNoFriendsWhenUserDontHaveFriends(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute(9, $this->friend));

        $response->assertOk()
            ->assertJsonCount(0, 'friends')
            ->assertJsonFragment([
                'count' => 0,
            ]);
    }

    public function testReturnOnlyCountWhenCountParamIsNotSpecifed(): void
    {
        Friendship::factory(3)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('api.users.getByCount', [
                'user' => $this->friend,
            ]));

        $response->assertOk()
            ->assertJsonCount(0, 'friends')
            ->assertJsonFragment([
                'count' => 3,
            ]);
    }

    public function testReturnedFriendHasCorrectAttributes(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute(8, $this->friend));

        $friend = User::findOrFail($friendship->friend_id);

        $response->assertOk()
            ->assertJsonCount(1, 'friends')
            ->assertJsonFragment([
                'id' => $friend->id,
                'name' => $friend->name,
                'profile_image' => $friend->profile_image,
            ])
            ->assertJsonFragment([
                'count' => 1,
            ]);
    }
}
