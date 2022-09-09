<?php

declare(strict_types=1);

namespace Tests\Feature\Messages;

use App\Enums\FriendshipStatus;
use App\Enums\MessageStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $route;

    private string $table = 'messages';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.messages.store');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertJsonValidationErrorFor('receiver_id')
            ->assertJsonValidationErrorFor('content');
    }

    public function testCannotCreateMessageWithEmptyText(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCannotCreateTooLongMessage(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCannotCreateMessageWithoutSpecificReceiver(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichNotExist(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
                'receiver_id' => 99999,
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForSelf(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
                'receiver_id' => $this->user->id,
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichIsNotFriend(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)->postJson($this->route, [
            'content' => 'Simple message',
            'receiver_id' => $friend->id,
        ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichRequestIsPending(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichRequestIsBlocked(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCanCreateMessageForFriend(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas($this->table, [
            'content' => 'Simple message',
        ]);
    }

    public function testAutoAddingSenderIdToMessageModelDuringCreatingProcess(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'sender_id' => $this->user->id,
            ]);
    }

    public function testPassedEmptyValuesAreTreatingAsNullValues(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => '',
                'receiver_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('content')
            ->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCreatedMessageHasDeliveredStatus(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple message',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'status' => MessageStatus::DELIVERED->value,
            ]);
    }
}
