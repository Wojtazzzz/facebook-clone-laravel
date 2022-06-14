<?php

namespace Tests\Feature\Messages;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $messagesStoreRoute;

    private string $messagesTable = 'messages';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
        $this->messagesStoreRoute = route('api.messages.store');
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->postJson($this->messagesStoreRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute);
        $response->assertUnprocessable();
    }

    public function testCannotCreateMessageWithEmptyText()
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'receiver_id' => $this->friend->id,
        ]);

        $response->assertJsonValidationErrorFor('text');
    }

    public function testCannotCreateTooLongMessage()
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'receiver_id' => $this->friend->id,
        ]);

        $response->assertJsonValidationErrorFor('text');
    }

    public function testCannotCreateMessageWithoutSpecificReceiver()
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
        ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichNotExist()
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
            'receiver_id' => 99999,
        ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForSelf()
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
            'receiver_id' => $this->user->id,
        ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichIsNotFriend()
    {
        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
            'receiver_id' => $this->friend->id,
        ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichRequestIsPending()
    {
        $this->generateFriendship(FriendshipStatus::PENDING);

        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
            'receiver_id' => $this->friend->id,
        ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCannotCreateMessageForReceiverWhichRequestIsBlocked()
    {
        $this->generateFriendship(FriendshipStatus::BLOCKED);

        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
            'receiver_id' => $this->friend->id,
        ]);

        $response->assertJsonValidationErrorFor('receiver_id');
    }

    public function testCanCreateMessageForFriend()
    {
        $this->generateFriendship(FriendshipStatus::CONFIRMED);

        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
            'receiver_id' => $this->friend->id,
        ]);

        $response->assertCreated();
    }

    public function testAutoAddingSenderIdToMessageModelDuringCreatingProcess()
    {
        $this->generateFriendship(FriendshipStatus::CONFIRMED);

        $response = $this->actingAs($this->user)->postJson($this->messagesStoreRoute, [
            'text' => 'Simple message',
            'receiver_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas($this->messagesTable, [
            'sender_id' => $this->user->id,
        ]);
    }

    private function generateFriendship(FriendshipStatus $status): void
    {
        Friendship::factory()->create([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => $status,
        ]);
    }
}
