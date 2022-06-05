<?php

namespace Tests\Feature\Friendship;

use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    public function testCannotUseWhenNotAuthorized()
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(401);
    }

    public function testCanUseWhenAuthorized()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200);
    }

    public function testReturnFriendsInvitedAndWhichSendInvites()
    {
        $user = User::factory()->createOne();
        $users = User::factory(50)->create();

        Friendship::factory(4)
            ->create([
                'user_id' => $user->id,
                'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'status' => 'CONFIRMED',
            ]);

        Friendship::factory(4)
            ->create([
                'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'friend_id' => $user->id,
                'status' => 'CONFIRMED',
            ]);

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200)
            ->assertJsonCount(8);
    }

    public function testReturnFriendsWhenUserHasOnlyInvitedFriends()
    {
        $user = User::factory()->createOne();
        $users = User::factory(50)->create();

        Friendship::factory(9)
            ->create([
                'user_id' => $user->id,
                'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'status' => 'CONFIRMED',
            ]);

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200)
            ->assertJsonCount(9);
    }

    public function testReturnFriendsWhenUserHasOnlyFriendsWhichInvite()
    {
        $user = User::factory()->createOne();
        $users = User::factory(50)->create();

        Friendship::factory(4)
            ->create([
                'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
                'friend_id' => $user->id,
                'status' => 'CONFIRMED',
            ]);

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200)
            ->assertJsonCount(4);
    }
}
