<?php

namespace Tests\Feature\Friendship;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized()
    {
        $response = $this->getJson("/api/friendship/friends/{$this->user->id}");
        $response->assertStatus(401);
    }

    public function testCanUseWhenAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson("/api/friendship/friends/{$this->user->id}");
        $response->assertStatus(200);
    }

    public function testReturnFriendsInvitedAndWhichSendInvites()
    {
        $users = User::factory(50)->create();

        Friendship::factory(4)
            ->create([
                'user_id' => $this->user->id,
                'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'status' => FriendshipStatus::CONFIRMED,
            ]);

        Friendship::factory(4)
            ->create([
                'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'friend_id' => $this->user->id,
                'status' => FriendshipStatus::CONFIRMED,
            ]);

        $response = $this->actingAs($this->user)->getJson("/api/friendship/friends/{$this->user->id}");

        $response->assertStatus(200)->assertJsonCount(8);
    }

    public function testReturnFriendsWhenUserHasOnlyInvitedFriends()
    {
        $users = User::factory(50)->create();

        Friendship::factory(9)
            ->create([
                'user_id' => $this->user->id,
                'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'status' => FriendshipStatus::CONFIRMED,
            ]);

        $response = $this->actingAs($this->user)->getJson("/api/friendship/friends/{$this->user->id}");

        $response->assertStatus(200)->assertJsonCount(9);
    }

    public function testReturnFriendsWhenUserHasOnlyFriendsWhichInvite()
    {
        $users = User::factory(50)->create();

        Friendship::factory(4)
            ->create([
                'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'friend_id' => $this->user->id,
                'status' => FriendshipStatus::CONFIRMED,
            ]);

        $response = $this->actingAs($this->user)->getJson("/api/friendship/friends/{$this->user->id}");

        $response->assertStatus(200)->assertJsonCount(4);
    }
}
