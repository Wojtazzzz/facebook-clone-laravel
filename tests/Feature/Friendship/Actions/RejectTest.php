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

    private string $rejectRoute;

    private string $friendshipsTable = 'friendships';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->rejectRoute = route('api.friendship.reject');
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->postJson($this->rejectRoute);
        $response->assertUnauthorized();
    }

    public function testCanRejectInvitation(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->rejectRoute, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->friendshipsTable, 1)
            ->assertDatabaseHas($this->friendshipsTable, [
                'user_id' => $friendship->user_id,
                'friend_id' => $this->user->id,
                'status' => FriendshipStatus::BLOCKED,
            ]);
    }

    public function testRejectInvitationNotSendsNotification(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->rejectRoute, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->notificationsTable, 0);
    }

    public function testPassedEmptyStringValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->rejectRoute, [
                'friend_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testCannotRejectInvitationWhichNotExists(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->postJson($this->rejectRoute, [
                'friend_id' => $friend->id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectOwn(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->rejectRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectInvitationWhichIsAlreadyConfirmed(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->rejectRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotRejectInvitationWhenInviterNotExistsNow(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => 99999,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->rejectRoute, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertUnprocessable();
    }
}
