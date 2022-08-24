<?php

declare(strict_types=1);

namespace Tests\Feature\Friendships\Invites;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private User $user;

    private Friendship $friendship;

    private string $route;

    private string $friendshipsTable = 'friendships';

    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);
        $this->route = route('api.invites.update', ['user' => $this->friendship->user_id]);
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->putJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanAcceptInvitation(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->route, [
                'status' => 'CONFIRMED',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->friendship->user_id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testCanRejectInvitation(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->route, [
                'status' => 'BLOCKED',
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->friendshipsTable, 1);
        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->friendship->user_id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);
    }

    public function testCannotPassPendingAsStatusType(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->route, [
                'status' => 'PENDING',
            ]);

        $response->assertJsonValidationErrorFor('status');

        $this->assertDatabaseCount($this->friendshipsTable, 1);
        $this->assertDatabaseHas($this->friendshipsTable, [
            'user_id' => $this->friendship->user_id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);
    }

    public function testAcceptInvitationSendsNotification(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->route, [
                'status' => 'CONFIRMED',
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->notificationsTable, 1)
            ->assertDatabaseHas($this->notificationsTable, [
                'notifiable_id' => $this->friendship->user_id,
            ]);
    }

    public function testRejectInvitationNotSendsNotification(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson($this->route, [
                'friend_id' => $friendship->user_id,
                'status' => 'BLOCKED',
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->notificationsTable, 0);
    }

    public function testPassedEmptyStringValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->route, [
                'status' => '',
            ]);

        $response->assertJsonValidationErrorFor('status');
    }

    public function testCannotAcceptInvitationWhichNotExists(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->putJson(route('api.invites.update', ['user' => $friend->id]), [
                'status' => 'CONFIRMED',
            ]);

        $response->assertNotFound();
    }

    public function testStatusMustBePassed(): void
    {
        Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson($this->route);

        $response->assertJsonValidationErrorFor('status');

        $this->assertDatabaseMissing($this->friendshipsTable, [
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testCannotAcceptOwn(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.invites.update', ['user' => $friendship->friend_id]), [
                'status' => 'CONFIRMED',
            ]);

        $response->assertNotFound();
    }

    public function testCannotRejectOwn(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.invites.update', ['user' => $friendship->friend_id]), [
                'status' => 'BLOCKED',
            ]);

        $response->assertNotFound();
    }

    public function testCannotAcceptInvitationWhichIsAlreadyConfirmed(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.invites.update', ['user' => $friendship->user_id]), [
                'status' => 'CONFIRMED',
            ]);

        $response->assertNotFound();
    }

    public function testCannotRejectInvitationWhichIsAlreadyConfirmed(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.invites.update', ['user' => $friendship->user_id]), [
                'status' => 'BLOCKED',
            ]);

        $response->assertNotFound();
    }

    public function testCannotAcceptInvitationWhenInitiatorNotExistsNow(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => 99999,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.invites.update', ['user' => $friendship->user_id]), [
                'status' => 'CONFIRMED',
            ]);

        $response->assertNotFound();
    }

    public function testCannotRejectInvitationWhenInitiatorNotExistsNow(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => 99999,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.invites.update', ['user' => $friendship->user_id]), [
                'status' => 'BLOCKED',
            ]);

        $response->assertNotFound();
    }
}
