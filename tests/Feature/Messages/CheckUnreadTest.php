<?php

declare(strict_types=1);

namespace Tests\Feature\Messages;

use App\Models\Message;
use App\Models\User;
use Tests\TestCase;

class CheckUnreadTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.messages.checkUnread');
    }

    public function testCannotUseAsUnathorized(): void
    {
        $response = $this->getJson($this->route);

        $response->assertUnauthorized();
    }

    public function testReturnTrueIfHasUnreadMessageWithOneUser(): void
    {
        Message::factory()->createOne([
            'receiver_id' => $this->user,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnTrueIfHasUnreadMessagesWithOneUser(): void
    {
        $friend = User::factory()->createOne();

        Message::factory(5)->create([
            'receiver_id' => $this->user,
            'sender_id' => $friend,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnTrueIfHasUnreadMessagesWithMoreUsers(): void
    {
        Message::factory(5)->create([
            'receiver_id' => $this->user,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnTrueIfHasUnreadAndReadMessagesWithOneUser(): void
    {
        $friend = User::factory()->createOne();

        Message::factory(3)->create([
            'receiver_id' => $this->user,
            'sender_id' => $friend,
        ]);

        Message::factory(3)->create([
            'receiver_id' => $this->user,
            'sender_id' => $friend,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnTrueIfHasUnreadReceivedMessageAndReadSentMessage(): void
    {
        $friend = User::factory()->createOne();

        Message::factory()->createOne([
            'receiver_id' => $this->user,
            'sender_id' => $friend,
        ]);

        Message::factory(3)->createOne([
            'receiver_id' => $friend,
            'sender_id' => $this->user,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnFalseIfNoReceivedMessages(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }

    public function testReturnFalseIfNoUnreadMessageWithOneUser(): void
    {
        Message::factory()->createOne([
            'receiver_id' => $this->user,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }

    public function testReturnTrueIfNoUnreadMessagesWithOneUser(): void
    {
        $friend = User::factory()->createOne();

        Message::factory(5)->create([
            'receiver_id' => $this->user,
            'sender_id' => $friend,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }

    public function testReturnFalseIfNoUnreadMessagesWithMoreUsers(): void
    {
        Message::factory(4)->create([
            'receiver_id' => $this->user,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }

    public function testReturnFalseIfHasReadMessagesWithMoreUsers(): void
    {
        Message::factory(3)->create([
            'receiver_id' => $this->user,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }

    public function testReturnFalseIfHasUnreadSentMessageAndReadReceivedMessage(): void
    {
        $friend = User::factory()->createOne();

        Message::factory()->createOne([
            'receiver_id' => $this->user,
            'sender_id' => $friend,
            'read_at' => now(),
        ]);

        Message::factory(3)->createOne([
            'receiver_id' => $friend,
            'sender_id' => $this->user,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }
}
