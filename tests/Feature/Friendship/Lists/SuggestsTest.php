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

        $response = $this->actingAs($user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function test_not_fetch_user_friends()
    {
        $user = User::factory()->createOne();
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => 'CONFIRMED',
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $user->id,
            'status' => 'CONFIRMED',
        ]);

        $response = $this->actingAs($user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)
            ->assertJsonCount(8);
    }

    public function test_not_fetch_users_where_request_is_pending()
    {
        $user = User::factory()->createOne();
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => 'PENDING',
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $user->id,
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)
            ->assertJsonCount(8);
    }

    public function test_not_fetch_blocked_users()
    {
        $user = User::factory()->createOne();
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => 'BLOCKED',
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $user->id,
            'status' => 'BLOCKED',
        ]);

        $response = $this->actingAs($user)->getJson('/api/friendship/suggests');

        $response->assertStatus(200)
            ->assertJsonCount(8);
    }
}
