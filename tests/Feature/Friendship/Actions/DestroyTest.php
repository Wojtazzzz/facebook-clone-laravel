<?php

declare(strict_types=1);

namespace Tests\Feature\Friendship\Actions;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;

    private string $route;
    private string $table = 'friendships';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.friendship.destroy');
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanDestroyFriendshipWhichUserInitialize(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertOk();

        $this->assertDatabaseMissing($this->table, [
            'user_id' => $this->user->id,
            'friend_id' => $friendship->friend_id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testCanDestroyFriendshipWhichFriendInitialize(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friendship->user_id,
            ]);

        $response->assertOk();

        $this->assertDatabaseMissing($this->table, [
            'user_id' => $friendship->user_id,
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    public function testErrorMessageWhenNoIdPassed(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testPassedEmptyStringValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testFriendNotExists(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => 25,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => 25,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testCannotDestroyFriendshipWhichNotExists(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friend->id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }

    public function testCannotDestroyFriendshipWhichIsPending(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'friend_id' => $friendship->friend_id,
            ]);

        $response->assertJsonValidationErrorFor('friend_id');
    }
}
