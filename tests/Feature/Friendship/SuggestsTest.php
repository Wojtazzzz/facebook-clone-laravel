<?php

namespace Tests\Feature\Friendship;

use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class SuggestsTest extends TestCase
{
    public function test_cannot_use_when_not_authorized()
    {
        User::factory()->createOne();

        $response = $this->getJson('/api/friendship/suggests');

        $response->assertStatus(401);
    }

    public function test_can_use_when_authorized()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200);
    }

    public function test_not_fetch_logged_user()
    {
        $user = User::factory()->createOne();
        User::factory(6)->create();

        $response = $this->actingAs($user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)
            ->assertJsonMissing([
                'id' => $user->id,
                'name' => "$user->first_name $user->last_name",
                'first_name' => $user->first_name,
                'profile_image' => $user->profile_image,
                'background_image' => $user->background_image,
            ]);
    }

    // public function test_not_fetch_users_invited_and_which_invites()
    // {
    //     $user = User::factory()->createOne();
    //     User::factory(10)->create();

    //     Friendship::factory(2)->create([
    //         'user_id' => $user->id
    //     ]);

    //     Friendship::factory(2)->create([
    //         'friend_id' => $user->id
    //     ]);

    //     $response = $this->actingAs($user)->getJson('/api/friendship/suggests');

    //     $response->assertStatus(200)
    //         ->assertJsonCount(6);
    // }
}
