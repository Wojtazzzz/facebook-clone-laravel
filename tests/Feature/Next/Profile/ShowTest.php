<?php

declare(strict_types=1);

namespace Tests\Feature\Next\Profile;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Tests\TestCase;

class ShowTest extends TestCase
{
    private User $user;
    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.next.profile', [
            'user' => $this->user,
        ]);
    }

    public function testResponseReturnProperlyData(): void
    {
        Friendship::factory(12)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $response = $this->getJson($this->route);
        $response->assertOk()
            ->assertJsonFragment([
                'user' => [
                    'background_image' => $this->user->background_image,
                    'first_name' => $this->user->first_name,
                    'id' => $this->user->id,
                    'name' => "{$this->user->first_name} {$this->user->last_name}",
                    'profile_image' => $this->user->profile_image,
                ],
            ])
            ->assertJsonFragment([
                'amount' => 12,
            ])
            ->assertJsonCount(12, 'friends.list');
    }

    public function testResponseCanReturnEmptyFriendsListWhenUserHasNoFriends(): void
    {
        $response = $this->getJson($this->route);
        $response->assertOk()
            ->assertJsonFragment([
                'amount' => 0,
            ])
            ->assertJsonCount(0, 'friends.list');
    }
}
