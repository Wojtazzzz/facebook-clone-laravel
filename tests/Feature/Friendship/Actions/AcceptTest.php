<?php

declare(strict_types=1);

namespace Tests\Feature\Friendship\Actions;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipRequestAccepted;
use Tests\TestCase;

class AcceptTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $acceptRoute;

    private string $friendshipsTable = 'friendships';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->acceptRoute = route('api.friendship.accept');
        $this->friend = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->postJson($this->acceptRoute);
        $response->assertUnauthorized();
    }

    public function testCanAcceptInvitation(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->acceptRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseCount($this->friendshipsTable, 1);
        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testAcceptInvitationSendsNotification(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->acceptRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseCount($this->notificationsTable, 1);
        $this->assertDatabaseHas($this->notificationsTable, [
            'type' => FriendshipRequestAccepted::class,
            'notifiable_id' => $this->friend->id,
        ]);
    }

    public function testCannotAcceptInvitationWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->acceptRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotAcceptOwn(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->acceptRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotAcceptInvitationWhichIsAlreadyConfirmed(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->acceptRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotAcceptInvitationWhenInviterNotExistsNow(): void
    {
        Friendship::factory()->createOne([
            'user_id' => 99999,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->acceptRoute, [
            'friend_id' => 99999,
        ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }
}
