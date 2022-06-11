<?php

namespace Tests\Feature\Friendship;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class SuggestsTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized()
    {
        $response = $this->getJson('/api/friendship/suggests');
        $response->assertStatus(401);
    }

    public function testCanUseWhenAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson('/api/friendship/suggests');
        $response->assertStatus(200);
    }

    public function testNotFetchLoggedUser()
    {
        $response = $this->actingAs($this->user)->getJson('/api/friendship/suggests');
        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function testNotFetchUserFriends()
    {
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)->assertJsonCount(8);
    }

    public function testNotFetchUsersWhereRequestIsPending()
    {
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::PENDING,
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)->assertJsonCount(8);
    }

    public function testNotFetchBlockedUsers()
    {
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::BLOCKED,
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)->assertJsonCount(8);
    }
}
