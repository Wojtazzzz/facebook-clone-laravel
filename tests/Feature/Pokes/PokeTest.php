<?php

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

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->postJson($this->pokeRoute);

        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->friend->id,
        ]);

        $response->assertCreated();
    }

    public function testCannotPassNoUserId()
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute);

        $response->assertUnprocessable();
    }

    public function testCannotPassUserIdWhichIsNotYourFriend()
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotPassUserIdWhichNotExist()
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => 99999,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotPassOwnId()
    {
        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->user->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCreateNewPokeWhenNoPokesWithSameFriendYet()
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);

        $this->assertDatabaseCount($this->pokesTable, 0);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testWhoSentFriendshipRequestMakesNoOdds()
    {
        $this->createFriendship($this->friend->id, $this->user->id, FriendshipStatus::CONFIRMED);

        $this->assertDatabaseCount($this->pokesTable, 0);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testCannotPokeUserWhoseRequestIsPending()
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::PENDING);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotPokeUserWhoseRequestIsBlocked()
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::BLOCKED);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testUpdateOldPokeWhenAlreadyHasPokesWithSameFriend()
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
            'user_id' => $this->friend->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->pokesTable, 1)
            ->assertDatabaseHas($this->pokesTable, ['count' => $count + 1]);
    }

    public function testCannotPokeWhenFriendNotRespondeForUserPokeYet()
    {
        $this->createFriendship($this->user->id, $this->friend->id, FriendshipStatus::CONFIRMED);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'latest_initiator_id' => $this->user->id,
        ]);

        $this->assertDatabaseCount($this->pokesTable, 1);

        $response = $this->actingAs($this->user)->postJson($this->pokeRoute, [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount($this->pokesTable, 1);
    }
}
