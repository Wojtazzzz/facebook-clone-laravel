<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\FriendshipRequestAccepted;
use App\Notifications\FriendshipRequestSent;
use App\Notifications\Poked;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.notifications.index');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk();
    }

    public function testReturnProperlyNotificationsAmount(): void
    {
        Notification::factory(8)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(8, 'data');
    }

    public function testReturnMaxFiveteeenNotifications(): void
    {
        Notification::factory(16)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(15, 'data');
    }

    public function testCanFetchMoreNotificationsFromSecondPage(): void
    {
        Notification::factory(16)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testReturnEmptyListWhenNoNotifications(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testFriendshipRequestAcceptedReturnProperlyData(): void
    {
        $friend = User::factory()->createOne();

        $this->user->notify(new FriendshipRequestAccepted($friend->id));

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'message' => 'Accepted your friendship invitation',
                'friend' => [
                    'background_image' => $friend->background_image,
                    'first_name' => $friend->first_name,
                    'id' => $friend->id,
                    'name' => "{$friend->first_name} {$friend->last_name}",
                    'profile_image' => $friend->profile_image,
                ],
                'link' => "/profile/{$friend->id}",
            ]);
    }

    public function testFriendshipRequestSentReturnProperlyData(): void
    {
        $friend = User::factory()->createOne();

        $this->user->notify(new FriendshipRequestSent($friend->id));

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'message' => 'Sent you a friendship invitation',
                'friend' => [
                    'background_image' => $friend->background_image,
                    'first_name' => $friend->first_name,
                    'id' => $friend->id,
                    'name' => "{$friend->first_name} {$friend->last_name}",
                    'profile_image' => $friend->profile_image,
                ],
                'link' => '/friends/invites',
            ]);
    }

    public function testPokedNotificationReturnProperlyData(): void
    {
        $friend = User::factory()->createOne();

        $this->user->notify(new Poked($friend->id, 50));

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'message' => 'Poked you 50 times in a row',
                'friend' => [
                    'background_image' => $friend->background_image,
                    'first_name' => $friend->first_name,
                    'id' => $friend->id,
                    'name' => "{$friend->first_name} {$friend->last_name}",
                    'profile_image' => $friend->profile_image,
                ],
                'link' => '/friends/pokes',
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        Notification::factory(2)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasSecondPage(): void
    {
        Notification::factory(12)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(12, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testSecondPageReturnProperlyPaginationData(): void
    {
        Notification::factory(23)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');

        $response->assertOk()
            ->assertJsonCount(8, 'data')
            ->assertJsonFragment([
                'current_page' => 2,
                'next_page' => null,
                'prev_page' => 1,
            ]);
    }
}
