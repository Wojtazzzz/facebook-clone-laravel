<?php

namespace Tests\Feature\Friendship\Actions;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class RejectTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $friendshipsTable = 'friendships';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized()
    {
        $response = $this->postJson('/api/friendship/reject');
        $response->assertStatus(401);
    }

    public function testCanRejectInvitation()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/reject', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->friendshipsTable, 1);
        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);
    }

    public function testRejectInvitationNotSendsNotification()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/reject', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->notificationsTable, 0);
    }

    public function testCannotRejectInvitationWhichNotExists()
    {
        $response = $this->actingAs($this->user)->postJson('/api/friendship/reject', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectOwn()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/reject', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectInvitationWhichIsAlreadyConfirmed()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/reject', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectInvitationWhenInviterNotExistsNow()
    {
        Friendship::factory()->createOne([
            'user_id' => 99999,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/reject', [
            'user_id' => 99999,
        ]);

        $response->assertUnprocessable();
    }
}
