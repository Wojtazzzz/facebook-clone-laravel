<?php

declare(strict_types=1);

namespace Tests\Feature\Messages;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class MessengerTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friends = User::factory(60)->create();
        $this->route = route('api.messages.messenger');
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

    public function testCanMaxFetchTenUsers(): void
    {
        $this->generateFriends(12);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchMoreOneSecondPage(): void
    {
        $this->generateFriends(14);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(4);
    }

    public function testReturnEmptyResponseWhenNoFriends(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    private function generateFriends(int $count): void
    {
        Friendship::factory($count)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }
}
