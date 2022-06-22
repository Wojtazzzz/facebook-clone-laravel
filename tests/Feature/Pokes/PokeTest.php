<?php

declare(strict_types=1);

namespace Tests\Feature\Pokes;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\Poke;
use App\Models\User;
use Tests\TestCase;

class PokeTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $pokeRoute;

    private string $pokesTable = 'pokes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
        $this->pokeRoute = route('api.pokes.poke');
    }

    private function createFriendship(int $userId, int $friendId, FriendshipStatus $status)
    {
        Friendship::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => $status,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->pokeRoute);

        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertCreated();
    }

    public function testCannotPassNoUserId(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute);

        $response->assertUnprocessable();
    }

    public function testCannotPassUserIdWhichIsNotYourFriend(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotPassUserIdWhichNotExist(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => 99999,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotPassOwnId(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->user->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCreateNewPokeWhenNoPokesWithSameFriendYet(): void
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);

        $this->assertDatabaseCount($this->pokesTable, 0);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testWhoSentFriendshipRequestMakesNoOdds(): void
    {
        $this->createFriendship($this->friend->id, $this->user->id, FriendshipStatus::CONFIRMED);

        $this->assertDatabaseCount($this->pokesTable, 0);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testCannotPokeUserWhoseRequestIsPending(): void
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::PENDING);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotPokeUserWhoseRequestIsBlocked(): void
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::BLOCKED);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testUpdateOldPokeWhenAlreadyHasPokesWithSameFriend(): void
    {
        $count = 20;
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'latest_initiator_id' => $this->friend->id,
            'count' => $count,
        ]);

        $this->assertDatabaseCount($this->pokesTable, 1);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->pokesTable, 1)
            ->assertDatabaseHas($this->pokesTable, ['count' => $count + 1]);
    }

    public function testCannotPokeWhenFriendNotRespondeForUserPokeYet(): void
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'latest_initiator_id' => $this->user->id,
        ]);

        $this->assertDatabaseCount($this->pokesTable, 1);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testOnePokeDontImpactOnOtherPokes(): void
    {
        $secondFriend = User::factory()->createOne();

        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);
        $this->createFriendship($this->user->id, $secondFriend->id, FriendshipStatus::CONFIRMED);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'latest_initiator_id' => $this->friend->id,
        ]);

        $dataForSecondPoke = [
            'user_id' => $this->user->id,
            'friend_id' => $secondFriend->id,
            'latest_initiator_id' => $secondFriend->id,
            'count' => 50,
        ];

        Poke::create($dataForSecondPoke);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'friend_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->pokesTable, 2)
            ->assertDatabaseHas($this->pokesTable, $dataForSecondPoke);
    }
}
