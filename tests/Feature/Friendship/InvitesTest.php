<?php

namespace Tests\Feature\Friendship;

use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class InvitesTest extends TestCase
{
    public function test_cannot_use_when_not_authorized()
    {
        User::factory()->createOne();

        $response = $this->getJson('/api/friendship/invites');

        $response->assertStatus(401);
    }

    public function test_can_use_when_authorized()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->getJson('/api/friendship/invites');

        $response->assertStatus(200);
    }

    public function test_fetch_received_invites()
    {
        $user = User::factory()->createOne();
        $users = User::factory(20)->create();

        Friendship::factory(5)->create([
            'friend_id' => $user->id,
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => 'PENDING'
        ]);

        $response = $this->actingAs($user)->getJson('/api/friendship/invites');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_not_fetch_sent_invites()
    {
        $user = User::factory()->createOne();
        $users = User::factory(20)->create();

        Friendship::factory()->create([
            'user_id' => $user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => 'PENDING'
        ]);

        $response = $this->actingAs($user)->getJson('/api/friendship/invites');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }
}
