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

    private string $acceptRoute;

    private string $friendshipsTable = 'friendships';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->acceptRoute = route('api.friendship.accept');
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->postJson($this->acceptRoute);
        $response->assertUnauthorized();
    }

    public function testCanAcceptInvitation(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->acceptRoute, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->friendshipsTable, 1);
        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $friendship->user_id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testAcceptInvitationSendsNotification(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->acceptRoute, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->notificationsTable, 1)
            ->assertDatabaseHas($this->notificationsTable, [
                'type' => FriendshipRequestAccepted::class,
                'notifiable_id' => $friendship->user_id,
            ]);
    }

    public function testPassedEmptyStringValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->acceptRoute, [
                'friend_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testCannotAcceptInvitationWhichNotExists(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->postJson($this->acceptRoute, [
                'friend_id' => $friend->id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotAcceptOwn(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->acceptRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotAcceptInvitationWhichIsAlreadyConfirmed(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->acceptRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotAcceptInvitationWhenInviterNotExistsNow(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => 99999,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->acceptRoute, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }
}
