<?php

namespace Tests\Feature\Friendship;

use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class InvitesTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized()
    {
        $response = $this->getJson('/api/friendship/invites');
        $response->assertStatus(401);
    }

    public function testCanUseWhenAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson('/api/friendship/invites');
        $response->assertStatus(200);
    }

    public function testFetchReceivedInvites()
    {
        $users = User::factory(20)->create();

        Friendship::factory(5)->create([
            'friend_id' => $this->user->id,
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/friendship/invites');

        $response->assertStatus(200)->assertJsonCount(5);
    }

    public function testNotFetchSentInvites()
    {
        $users = User::factory(20)->create();

        Friendship::factory()->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/friendship/invites');

        $response->assertStatus(200)->assertJsonCount(0);
    }
}
