<?php

namespace Tests\Feature\Messages;

use App\Models\Message;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $messagesIndexRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
        $this->messagesIndexRoute = route('api.messages.index', $this->friend);
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->getJson($this->messagesIndexRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson($this->messagesIndexRoute);
        $response->assertOk();
    }

    public function testCanReturnOnlySentMessages()
    {
        Message::factory(20)->create([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->friend->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->messagesIndexRoute);

        $response->assertOk()
            ->assertJsonCount(15);
    }

    public function testCanReturnOnlyReceivedMessages()
    {
        Message::factory(20)->create([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->messagesIndexRoute);

        $response->assertOk()
            ->assertJsonCount(15);
    }

    public function testCanReturnSentAndReceivedMessages()
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->messagesIndexRoute);

        $response->assertOk()
            ->assertJsonFragment([
                'isReceived' => true,
            ])
            ->assertJsonFragment([
                'isReceived' => false,
            ]);
    }

    public function testReturnMaxFiveteenMessages()
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->messagesIndexRoute);
        $response->assertOk()
            ->assertJsonCount(15);
    }

    public function testCanFetchMoreMessagesOnSecondPage()
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->messagesIndexRoute.'?page=2');
        $response->assertOk()
            ->assertJsonCount(15);
    }

    private function generateMessages(int $perUser = 15): void
    {
        Message::factory($perUser)->create([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->friend->id,
            'created_at' => fn () => now()->subHours(rand(1, 3)),
        ]);

        Message::factory($perUser)->create([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
            'created_at' => fn () => now()->subHours(rand(1, 3)),
        ]);
    }
}
