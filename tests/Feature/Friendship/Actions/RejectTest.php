<?php

declare(strict_types=1);

namespace Tests\Feature\Friendship\Actions;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class RejectTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $rejectRoute;

    private string $friendshipsTable = 'friendships';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->rejectRoute = route('api.friendship.reject');
        $this->friend = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->postJson($this->rejectRoute);
        $response->assertUnauthorized();
    }

    public function testCanRejectInvitation(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->rejectRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseCount($this->friendshipsTable, 1);
        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);
    }

    public function testRejectInvitationNotSendsNotification(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->rejectRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseCount($this->notificationsTable, 0);
    }

    public function testCannotRejectInvitationWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->rejectRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectOwn(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->rejectRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectInvitationWhichIsAlreadyConfirmed(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->rejectRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectInvitationWhenInviterNotExistsNow(): void
    {
        Friendship::factory()->createOne([
            'user_id' => 99999,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->rejectRoute, [
            'friend_id' => 99999,
        ]);

        $response->assertUnprocessable();
    }
}
