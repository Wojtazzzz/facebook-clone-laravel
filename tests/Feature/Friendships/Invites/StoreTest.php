<?php

declare(strict_types=1);

namespace Tests\Feature\Friendships\Invites;


use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipRequestSent;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $route;
    private string $friendshipsTable = 'friendships';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.invites.store', $this->user->id);
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanSendInvitation(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friend->id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->user->id,
            'friend_id' => $friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);
    }

    public function testPassedEmptyStringValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testCannotInviteSelf(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $this->user->id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testInvitationCreatesNotification(): void
    {
        $friend = User::factory()->createOne();

        $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friend->id,
            ]);

        $this->assertDatabaseCount($this->notificationsTable, 1)
            ->assertDatabaseHas($this->notificationsTable, [
                'type' => FriendshipRequestSent::class,
                'notifiable_id' => $friend->id,
            ]);
    }

    public function testCannotSendSecondInvitation(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');

        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotSendInvitationWhenFriendSentItYet(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');

        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotSendInvitationWhenAlreadyHaveFriendshipWithThisFriend(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');

        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotSendInvitationWhenAlreadyHaveBlockedFriendshipWithThisFriend(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');

        $this->assertDatabaseCount($this->friendshipsTable, 1);
    }

    public function testCannotInviteUserWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => 99999,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }
}
