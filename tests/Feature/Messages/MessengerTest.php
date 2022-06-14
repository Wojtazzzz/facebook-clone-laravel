<?php

namespace Tests\Feature\Messages;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class MessengerTest extends TestCase
{
    private User $user;
    private Collection $friends;

    private string $messengerRoute;

    private string $messagesTable = 'messages';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friends = User::factory(60)->create();
        $this->messengerRoute = route('api.messages.messenger');
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->getJson($this->messengerRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson($this->messengerRoute);
        $response->assertOk();
    }

    public function testCanMaxFetchTenUsers()
    {
        $this->generateFriends(30);

        $response = $this->actingAs($this->user)->getJson($this->messengerRoute);
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchMoreOneSecondPage()
    {
        $this->generateFriends(30);

        $response = $this->actingAs($this->user)->getJson($this->messengerRoute.'?page=2');
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testReturnEmptyResponseWhenNoFriends()
    {
        $response = $this->actingAs($this->user)->getJson($this->messengerRoute);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    private function generateFriends(int $count): void
    {
        Friendship::factory($count)->create([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }
}
