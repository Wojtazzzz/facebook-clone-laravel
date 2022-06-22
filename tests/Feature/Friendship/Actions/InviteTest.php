<?php

declare(strict_types=1);

namespace Tests\Feature\Friendship\Actions;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipRequestSent;
use Tests\TestCase;

class InviteTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $inviteRoute;

    private string $friendshipsTable = 'friendships';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->inviteRoute = route('api.friendship.invite', $this->user->id);
        $this->friend = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->postJson($this->inviteRoute);
        $response->assertUnauthorized();
    }

    public function testCanSendInvitation(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);
    }

    public function testPassedEmptyStringValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => '',
        ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testCannotInviteSelf(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => $this->user->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testInvitationCreatesNotification(): void
    {
        $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $this->assertDatabaseCount($this->notificationsTable, 1);
        $this->assertDatabaseHas($this->notificationsTable, [
            'type' => FriendshipRequestSent::class,
            'notifiable_id' => $this->friend->id,
        ]);
    }

    public function testCannotSendSecondInvitation(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotSendInvitationWhenFriendSentItYet(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotSendInvitationWhenAlreadyHaveFriendshipWithThisFriend(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotSendInvitationWhenAlreadyHaveBlockedFriendshipWithThisFriend(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotInviteUserWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->inviteRoute, [
            'friend_id' => 99999,
        ]);

        $response->assertUnprocessable();
    }
}
