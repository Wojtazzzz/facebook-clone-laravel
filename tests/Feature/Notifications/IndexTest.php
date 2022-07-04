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

    private string $notificationsRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->notificationsRoute = route('api.notifications.index');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->notificationsRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk();
    }

    public function testReturnProperlyNotificationsAmount(): void
    {
        Notification::factory(8)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk()
            ->assertJsonCount(8);
    }

    public function testReturnMaxTenNotifications(): void
    {
        Notification::factory(11)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchMoreNotificationsFromSecondPage(): void
    {
        Notification::factory(16)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute.'?page=2');
        $response->assertOk()
            ->assertJsonCount(6);
    }

    public function testReturnEmptyListWhenNoNotifications(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute.'?page=2');
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testFriendshipRequestAcceptedReturnProperlyData(): void
    {
        $friend = User::factory()->createOne();

        $this->user->notify(new FriendshipRequestAccepted($friend->id));

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk()
            ->assertJsonCount(1)
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

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk()
            ->assertJsonCount(1)
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

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk()
            ->assertJsonCount(1)
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
}
