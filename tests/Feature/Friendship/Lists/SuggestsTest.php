<?php

declare(strict_types=1);

namespace Tests\Feature\Friendship\Lists;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class SuggestsTest extends TestCase
{
    private User $user;

    private string $suggestsRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->suggestsRoute = route('api.friendship.suggests');
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->getJson($this->suggestsRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseWhenAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->suggestsRoute);
        $response->assertOk();
    }

    public function testNotFetchLoggedUser(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->suggestsRoute);
        $response->assertOk()->assertJsonCount(0);
    }

    public function testNotFetchUserFriends(): void
    {
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->suggestsRoute);

        $response->assertOk()->assertJsonCount(8);
    }

    public function testNotFetchUsersWhereRequestIsPending(): void
    {
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::PENDING,
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->suggestsRoute);

        $response->assertOk()->assertJsonCount(8);
    }

    public function testNotFetchBlockedUsers(): void
    {
        $users = User::factory(12)->create();

        Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'status' => FriendshipStatus::BLOCKED,
        ]);

        Friendship::factory(2)->create([
            'user_id' => fn () => $this->faker->unique->randomElement($users->pluck('id')),
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->suggestsRoute);

        $response->assertOk()->assertJsonCount(8);
    }

    public function testReturnMaxTenSuggests(): void
    {
        User::factory(18)->create();

        $response = $this->actingAs($this->user)->getJson($this->suggestsRoute);

        $response->assertOk()->assertJsonCount(10);
    }

    public function testCanFetchMoreSuggestsFromSecondPage(): void
    {
        User::factory(17)->create();

        $response = $this->actingAs($this->user)->getJson($this->suggestsRoute.'?page=2');

        $response->assertOk()->assertJsonCount(7);
    }
}
