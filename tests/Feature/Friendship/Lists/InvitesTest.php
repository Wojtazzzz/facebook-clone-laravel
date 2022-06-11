<?php

namespace Tests\Feature\Friendship;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class InvitesTest extends TestCase
{
    private User $user;

    private string $invitesRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->invitesRoute = route('api.friendship.invites');
    }

    public function testCannotUseWhenNotAuthorized()
    {
        $response = $this->getJson($this->invitesRoute);
        $response->assertStatus(401);
    }

    public function testCanUseWhenAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson($this->invitesRoute);
        $response->assertStatus(200);
    }

    public function testFetchReceivedInvites()
    {
        $users = User::factory(20)->create();

        Friendship::factory(5)->create([
            'friend_id' => $this->user->id,
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->invitesRoute);

        $response->assertStatus(200)->assertJsonCount(5);
    }

    public function testNotFetchSentInvites()
    {
        $users = User::factory(20)->create();

        Friendship::factory()->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->invitesRoute);

        $response->assertStatus(200)->assertJsonCount(0);
    }
}
