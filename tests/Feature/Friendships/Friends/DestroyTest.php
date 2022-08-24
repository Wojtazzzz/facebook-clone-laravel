<?php

declare(strict_types=1);

namespace Tests\Feature\Friendships\Friends;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;

    private Friendship $friendship;

    private string $route;

    private string $table = 'friendships';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
        $this->route = route('api.friends.destroy', ['user' => $this->friendship->friend_id]);
    }

    public function testCannotUseWhenNotAuthorized(): void
    {
        $response = $this->deleteJson($this->route);

        $response->assertUnauthorized();
    }

    public function testCanDestroyFriendshipWhichUserInitialize(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson($this->route, [
                'user' => $this->friendship->friend_id,
            ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCanDestroyFriendshipWhichFriendInitialize(): void
    {
        $friendship = Friendship::factory()->createOne([
            'friend_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $this->assertDatabaseCount($this->table, 2);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('api.friends.destroy', ['user' => $friendship->user_id]));

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassNoFriend(): void
    {
        $this->expectException(UrlGenerationException::class);
        $this->actingAs($this->user)->deleteJson(route('api.friends.destroy'));
    }

    public function testCannotPassFriendWhichNotExists(): void
    {
        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => 25,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('api.friends.destroy', ['user' => 25]));

        $response->assertNotFound();
    }

    public function testCannotDestroyFriendshipWhichNotExists(): void
    {
        $friend = User::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->deleteJson(route('api.friends.destroy', ['user' => $friend->id]));

        $response->assertNotFound();
    }

    public function testCannotDestroyFriendshipWhichIsPending(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('api.friends.destroy', ['user' => $friendship->friend_id]));

        $response->assertNotFound();
    }
}
