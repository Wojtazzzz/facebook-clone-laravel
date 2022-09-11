<?php

declare(strict_types=1);

namespace Tests\Feature\Birthday;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private Post $post;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.birthdays.index');
    }

    private function generateFriend(Carbon $born_at, int $amount = 1): void
    {
        User::factory($amount)->create([
            'born_at' => $born_at,
        ])->each(function (User $friend) {
            Friendship::factory()->create([
                'user_id' => $this->user->id,
                'friend_id' => $friend->id,
                'status' => FriendshipStatus::CONFIRMED,
            ]);
        });
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testReturnOnlyFriendsWhichHasBirthdayToday(): void
    {
        $this->generateFriend(now());
        $this->generateFriend(now()->subYear());
        $this->generateFriend(now()->subDay(), 2);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function testNotReturnUsersWhichAreNotFriends(): void
    {
        $this->generateFriend(now(), 3);

        User::factory(2)->create([
            'born_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function testReturnEmptyResponseWhenNoOneHasBirthday(): void
    {
        $this->generateFriend(now()->subDay(), 3);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testResponseHasUserIdAndName(): void
    {
        $this->generateFriend(now());

        $friend = User::whereNot('id', $this->user->id)->firstOrFail();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $friend->id,
                'name' => $friend->name,
            ]);
    }
}
