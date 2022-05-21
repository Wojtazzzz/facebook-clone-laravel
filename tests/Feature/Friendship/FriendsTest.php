<?php

namespace Tests\Feature\Friendship;

use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    public function test_cannot_use_when_not_authorized()
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(401);
    }

    public function test_can_use_when_authorized()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200);
    }

    public function test_return_friends_invited_and_which_send_invites()
    {
        $user = User::factory()->createOne();
        User::factory(10)->create();

        Friendship::factory(4)->create([
            'user_id' => $user->id,
            'status' => 'CONFIRMED'
        ]);

        Friendship::factory(4)->create([
            'friend_id' => $user->id,
            'status' => 'CONFIRMED'
        ]);

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200)
            ->assertJsonCount(8);
    }

    public function test_return_friends_when_user_has_only_invited_friends()
    {
        $user = User::factory()->createOne();
        User::factory(10)->create();

        Friendship::factory(9)->create([
            'user_id' => $user->id,
            'status' => 'CONFIRMED'
        ]);

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200)
            ->assertJsonCount(9);
    }

    public function test_return_friends_when_user_has_only_friends_which_invite()
    {
        $user = User::factory()->createOne();
        User::factory(10)->create();

        Friendship::factory(4)->create([
            'friend_id' => $user->id,
            'status' => 'CONFIRMED'
        ]);

        $response = $this->actingAs($user)->getJson("/api/friendship/friends/$user->id");

        $response->assertStatus(200)
            ->assertJsonCount(4);
    }
}
