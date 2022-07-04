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

    private string $pokeRoute;

    private string $pokesTable = 'pokes';
    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->pokeRoute = route('api.pokes.poke');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->pokeRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();
    }

    public function testCannotPassNoUserId(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute);
        $response->assertUnprocessable();
    }

    public function testPassedEmptyStringIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testCannotPassUserIdWhichIsNotYourFriend(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friend->id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotPassUserIdWhichNotExist(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => 99999,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotPassOwnId(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $this->user->id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCreateNewPokeWhenNoPokesWithSameFriendYet(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $this->assertDatabaseCount($this->pokesTable, 0);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testWhoSentFriendshipRequestMakesNoOdds(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $this->assertDatabaseCount($this->pokesTable, 0);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testCannotPokeUserWhoseRequestIsPending(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertUnprocessable();
    }

    public function testCannotPokeUserWhoseRequestIsBlocked(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::BLOCKED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertUnprocessable();
    }

    public function testUpdateOldPokeWhenAlreadyHasPokesWithSameFriend(): void
    {
        $count = 20;
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $friendship->friend_id,
            'latest_initiator_id' => $friendship->friend_id,
            'count' => $count,
        ]);

        $this->assertDatabaseCount($this->pokesTable, 1);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->pokesTable, 1)
            ->assertDatabaseHas($this->pokesTable, ['count' => $count + 1]);
    }

    public function testCannotPokeWhenFriendNotRespondeForUserPokeYet(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $friendship->friend_id,
            'latest_initiator_id' => $this->user->id,
        ]);

        $this->assertDatabaseCount($this->pokesTable, 1);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount($this->pokesTable, 1);
    }

    public function testOnePokeDontImpactOnOtherPokes(): void
    {
        $friendships = Friendship::factory(2)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $friendships[0]->friend_id,
            'latest_initiator_id' => $friendships[0]->friend_id,
        ]);

        $dataForSecondPoke = [
            'user_id' => $this->user->id,
            'friend_id' => $friendships[1]->friend_id,
            'latest_initiator_id' => $friendships[1]->id,
            'count' => 50,
        ];

        Poke::create($dataForSecondPoke);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendships[0]->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->pokesTable, 2)
            ->assertDatabaseHas($this->pokesTable, $dataForSecondPoke);
    }

    public function testPokeCreatesNotification(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->notificationsTable, 1);
    }

    public function testCreatedNotificationHasProperlyFirstPokeMessageFriendIdAndLink(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->notificationsTable, 1)
            ->assertDatabaseHas($this->notificationsTable, [
                'notifiable_id' => $friendship->friend_id,
                'data' => json_encode([
                    'friendId' => $this->user->id,
                    'message' => 'Poked you first time',
                    'link' => '/friends/pokes',
                ]),
            ]);
    }

    public function testCreatedNotificationHasProperlyPokeAgainPokeMessageWithIncrementedCount(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Poke::create([
            'user_id' => $this->user->id,
            'friend_id' => $friendship->friend_id,
            'latest_initiator_id' => $friendship->friend_id,
            'count' => 3,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->pokeRoute, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->notificationsTable, 1)
            ->assertDatabaseHas($this->notificationsTable, [
                'notifiable_id' => $friendship->friend_id,
                'data' => json_encode([
                    'friendId' => $this->user->id,
                    'message' => 'Poked you 4 times in a row',
                    'link' => '/friends/pokes',
                ]),
            ]);
    }
}
