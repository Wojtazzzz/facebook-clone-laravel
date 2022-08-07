<?php

declare(strict_types=1);

namespace Tests\Feature\Friendships\Suggests;

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
        $this->route = route('api.suggests.index');
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseWhenAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk();
    }

    public function testNotFetchLoggedUser(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(0, 'data');
    }

    public function testNotFetchUserFriends(): void
    {
        User::factory()->createOne();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Friendship::factory(2)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function testNotFetchUsersWhereRequestIsPending(): void
    {
        User::factory(3)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        Friendship::factory(2)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function testNotFetchBlockedUsers(): void
    {
        User::factory(8)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        Friendship::factory(2)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(8, 'data');
    }

    public function testReturnMaxTenSuggests(): void
    {
        User::factory(18)->create();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(10, 'data');
    }

    public function testCanFetchMoreSuggestsFromSecondPage(): void
    {
        User::factory(17)->create();

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()->assertJsonCount(7, 'data');
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        User::factory(2)->create();

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasSecondPage(): void
    {
        User::factory(12)->create();

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => 2,
                'prev_page' => null,
            ]);
    }

    public function testSecondPageReturnProperlyPaginationData(): void
    {
        User::factory(12)->create();

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'current_page' => 2,
                'next_page' => null,
                'prev_page' => 1,
            ]);
    }
}
