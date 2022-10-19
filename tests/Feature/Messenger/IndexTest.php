<?php

declare(strict_types=1);

namespace Tests\Feature\Messenger;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friends = User::factory(60)->create();
        $this->route = route('api.messenger.index');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanMaxFetchFiveteenUsers(): void
    {
        $this->generateFriends(17);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(15, 'data');
    }

    public function testCanFetchMoreOneSecondPage(): void
    {
        $this->generateFriends(18);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testReturnEmptyResponseWhenNoFriends(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    private function generateFriends(int $count): void
    {
        Friendship::factory($count)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }
}
