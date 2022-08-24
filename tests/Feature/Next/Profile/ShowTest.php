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
                    'created_at' => $this->user->created_at->format('F Y'),
                    'first_name' => $this->user->first_name,
                    'from' => $this->user->from,
                    'id' => $this->user->id,
                    'lives_in' => $this->user->lives_in,
                    'marital_status' => $this->user->marital_status,
                    'name' => "{$this->user->first_name} {$this->user->last_name}",
                    'profile_image' => $this->user->profile_image,
                    'went_to' => $this->user->went_to,
                    'works_at' => $this->user->works_at,
                ],
            ])
            ->assertJsonFragment([
                'amount' => 12,
            ])
            ->assertJsonCount(12, 'friends.list');

        $data = $response->json('friends.list');

        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('from', $data);
        $this->assertArrayNotHasKey('lives_in', $data);
        $this->assertArrayNotHasKey('marital_status', $data);
        $this->assertArrayNotHasKey('went_to', $data);
        $this->assertArrayNotHasKey('works_at', $data);
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
