<?php

declare(strict_types=1);

namespace Tests\Feature\Friendships\Invites;

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
        $this->route = route('api.invites.index');
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

    public function testFetchReceivedInvites(): void
    {
        Friendship::factory(5)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(5, 'data');
    }

    public function testNotFetchSentInvites(): void
    {
        Friendship::factory()->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(0, 'data');
    }

    public function testReturnMaxTenInvites(): void
    {
        Friendship::factory(12)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertJsonCount(10, 'data');
    }

    public function testCanFetchMoreInvitesFromSecondPage(): void
    {
        Friendship::factory(13)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        Friendship::factory(2)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

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
        Friendship::factory(12)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

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
        Friendship::factory(12)->create([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

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
