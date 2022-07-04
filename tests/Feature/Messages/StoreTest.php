<?php

declare(strict_types=1);

namespace Tests\Feature\Messages;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $messagesStoreRoute;

    private string $messagesTable = 'messages';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->messagesStoreRoute = route('api.messages.store');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->messagesStoreRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute);
        $response->assertJsonValidationErrorFor('receiver_id')
            ->assertJsonValidationErrorFor('text');
    }

    public function testCannotCreateMessageWithEmptyText(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->messagesStoreRoute, [
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('text');
    }

    public function testCannotCreateTooLongMessage(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->messagesStoreRoute, [
                'text' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('text');
    }

    public function testCannotCreateMessageWithoutSpecificReceiver(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->messagesStoreRoute, [
                'text' => 'Simple message',
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichNotExist(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->messagesStoreRoute, [
                'text' => 'Simple message',
                'receiver_id' => 99999,
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForSelf(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->messagesStoreRoute, [
                'text' => 'Simple message',
                'receiver_id' => $this->user->id,
            ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichIsNotFriend(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
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
            ->postJson($this->messagesStoreRoute, [
                'text' => 'Simple message',
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
            ->postJson($this->messagesStoreRoute, [
                'text' => 'Simple message',
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
            ->postJson($this->messagesStoreRoute, [
                'text' => 'Simple message',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();
    }

    public function testAutoAddingSenderIdToMessageModelDuringCreatingProcess(): void
    {
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->messagesStoreRoute, [
                'text' => 'Simple message',
                'receiver_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas($this->messagesTable, [
            'sender_id' => $this->user->id,
        ]);
    }

    public function testPassedEmptyValuesAreTreatingAsNullValues(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->messagesStoreRoute, [
                'text' => '',
                'receiver_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('text')
            ->assertJsonValidationErrorFor('receiver_id');
    }
}
