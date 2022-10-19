<?php

declare(strict_types=1);

namespace Tests\Feature\UserFriends;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private User $friend;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
    }

    private function getRoute(User $user): string
    {
        return route('api.users.friends.index', [
            'user' => $user,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->getRoute($this->friend));
        $response->assertUnauthorized();
    }

    public function testCanGetUserFriends(): void
    {
        Friendship::factory(2)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->friend));

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testCanGetOwnFriends(): void
    {
        Friendship::factory(9)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->user));

        $response->assertOk()
            ->assertJsonCount(9, 'data');
    }

    public function testReturnOnlyUsersWhichAreConfirmedFriends(): void
    {
        Friendship::factory(5)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Friendship::factory(8)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        Friendship::factory(3)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        Friendship::factory(7)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        User::factory(11)->create();

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->friend));

        $response->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function testReturnMax20Users(): void
    {
        Friendship::factory(24)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->friend));

        $response->assertOk()
            ->assertJsonCount(20, 'data');
    }

    public function testCanReturnEmptyDataWhenNoFriends(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->friend));

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        Friendship::factory(5)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->friend));

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasSecondPage(): void
    {
        Friendship::factory(26)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->friend));

        $response->assertOk()
            ->assertJsonCount(20, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => 2,
                'prev_page' => null,
            ]);
    }

    public function testSecondPageReturnProperlyPaginationData(): void
    {
        Friendship::factory(23)->create([
            'user_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute($this->friend).'?page=2');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment([
                'current_page' => 2,
                'next_page' => null,
                'prev_page' => 1,
            ]);
    }
}
