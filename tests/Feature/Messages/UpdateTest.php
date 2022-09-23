<?php

declare(strict_types=1);

namespace Tests\Feature\Messages;

use App\Models\Message;
use App\Models\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private User $user;

    private User $friend;

    private string $table = 'messages';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
    }

    private function getRoute(User $friend): string
    {
        return route('api.messages.update', [
            'user' => $friend,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->putJson($this->getRoute($this->friend));
        $response->assertUnauthorized();
    }

    public function testUpdateOneReceivedMessage(): void
    {
        Message::factory()->createOne([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ]);

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'read_at' => null,
            ]);

        $response = $this->actingAs($this->user)
            ->putJson($this->getRoute($this->friend));

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseMissing($this->table, [
                'read_at' => null,
            ]);
    }

    public function testUpdateManyReceivedMessagesFromUser(): void
    {
        Message::factory(4)->create([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ]);

        $this->assertDatabaseCount($this->table, 4)
            ->assertDatabaseHas($this->table, [
                'read_at' => null,
            ]);

        $response = $this->actingAs($this->user)
            ->putJson($this->getRoute($this->friend));

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 4)
            ->assertDatabaseMissing($this->table, [
                'read_at' => null,
            ]);
    }

    public function testUpdateOnlyMessagesFromSpecificUser(): void
    {
        $anotherFriend = User::factory()->createOne();

        Message::factory(4)->create([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ]);

        Message::factory(4)->create([
            'sender_id' => $anotherFriend->id,
            'receiver_id' => $this->user->id,
        ]);

        $this->assertDatabaseCount($this->table, 8)
            ->assertDatabaseHas($this->table, [
                'read_at' => null,
            ]);

        $response = $this->actingAs($this->user)
            ->putJson($this->getRoute($this->friend));

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 8)
            ->assertDatabaseMissing($this->table, [
                'sender_id' => $this->friend->id,
                'read_at' => null,
            ])
            ->assertDatabaseHas($this->table, [
                'sender_id' => $anotherFriend->id,
                'read_at' => null,
            ]);
    }

    public function testUpdateOnlyReceivedMessages(): void
    {
        $anotherFriend = User::factory()->createOne();

        Message::factory()->createOne([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ]);

        Message::factory()->createOne([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->friend->id,
        ]);

        Message::factory()->createOne([
            'sender_id' => $this->user->id,
            'receiver_id' => $anotherFriend->id,
        ]);

        $this->assertDatabaseCount($this->table, 3)
            ->assertDatabaseHas($this->table, [
                'read_at' => null,
            ]);

        $response = $this->actingAs($this->user)
            ->putJson($this->getRoute($this->friend));

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 3)
            ->assertDatabaseMissing($this->table, [
                'sender_id' => $this->friend->id,
                'read_at' => null,
            ])
            ->assertDatabaseHas($this->table, [
                'sender_id' => $this->user->id,
                'receiver_id' => $this->friend->id,
                'read_at' => null,
            ])
            ->assertDatabaseHas($this->table, [
                'sender_id' => $this->user->id,
                'receiver_id' => $anotherFriend->id,
                'read_at' => null,
            ]);
    }

    public function testUpdateOnlyReceivedMessagesFromLoggedUser(): void
    {
        $anotherFriend = User::factory()->createOne();

        Message::factory()->createOne([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ]);

        Message::factory()->createOne([
            'sender_id' => $this->friend->id,
            'receiver_id' => $anotherFriend->id,
        ]);

        $this->assertDatabaseCount($this->table, 2)
            ->assertDatabaseHas($this->table, [
                'read_at' => null,
            ]);

        $response = $this->actingAs($this->user)
            ->putJson($this->getRoute($this->friend));

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 2)
            ->assertDatabaseMissing($this->table, [
                'sender_id' => $this->friend->id,
                'receiver_id' => $this->user->id,
                'read_at' => null,
            ])
            ->assertDatabaseHas($this->table, [
                'sender_id' => $this->friend->id,
                'receiver_id' => $anotherFriend->id,
                'read_at' => null,
            ]);
    }
}
