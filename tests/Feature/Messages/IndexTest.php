<?php

declare(strict_types=1);

namespace Tests\Feature\Messages;

use App\Models\Message;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
        $this->route = route('api.messages.index', $this->friend);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk();
    }

    public function testCanReturnOnlySentMessages(): void
    {
        Message::factory(20)->create([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->friend->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function testCanReturnOnlyReceivedMessages(): void
    {
        Message::factory(20)->create([
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function testCanReturnSentAndReceivedMessages(): void
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonFragment([
                'isReceived' => true,
            ])
            ->assertJsonFragment([
                'isReceived' => false,
            ]);
    }

    public function testReturnMaxFiveteenMessages(): void
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function testCanFetchMoreMessagesOnSecondPage(): void
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        $this->generateMessages(2);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasSecondPage(): void
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => 2,
                'prev_page' => null,
            ]);
    }

    public function testSecondPageReturnProperlyPaginationData(): void
    {
        $this->generateMessages();

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');

        $response->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
                'next_page' => null,
                'prev_page' => 1,
            ]);
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
