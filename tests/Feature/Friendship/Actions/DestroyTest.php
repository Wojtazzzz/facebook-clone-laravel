<?php

namespace Tests\Feature\Friendship\Actions;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $friendshipsTable = 'friendships';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
    }

    public function testCannotUseWhenNotAuthorized()
    {
        $response = $this->postJson('/api/friendship/destroy');
        $response->assertStatus(401);
    }

    public function testCanDestroyFriendshipWhichUserInitialize()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/destroy', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseMissing($this->friendshipsTable, [
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testCanDestroyFriendshipWhichFriendInitialize()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/destroy', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseMissing($this->friendshipsTable, [
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testErrorMessageWhenNoIdPassed()
    {
        $response = $this->actingAs($this->user)->postJson('/api/friendship/destroy');
        $response->assertInvalid([
            'user_id' => 'The user id field is required',
        ]);
    }

    public function testFriendNotExists()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => 25,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/destroy', [
            'user_id' => 25,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotDestroyFriendshipWhichNotExists()
    {
        $response = $this->actingAs($this->user)->postJson('/api/friendship/destroy', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotDestroyFriendshipWhichIsPending()
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/friendship/destroy', [
            'user_id' => $this->friend->id,
        ]);

        $response->assertNotFound();
    }
}
