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
    private User $friend;

    private string $notificationsRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
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

    public function testReturnProperlyNotificationsNumber(): void
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
        Notification::factory(12)->create([
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
        $this->user->notify(new FriendshipRequestAccepted($this->friend->id));

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'message' => 'Accepted your friendship invitation',
                'friend' => [
                    'background_image' => $this->friend->background_image,
                    'first_name' => $this->friend->first_name,
                    'id' => $this->friend->id,
                    'name' => "{$this->friend->first_name} {$this->friend->last_name}",
                    'profile_image' => $this->friend->profile_image,
                ],
                'link' => "/profile/{$this->friend->id}",
            ]);
    }

    public function testFriendshipRequestSentReturnProperlyData(): void
    {
        $this->user->notify(new FriendshipRequestSent($this->friend->id));

        // $this->user->notify(new Poked($this->user->id, 50));

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'message' => 'Sent you a friendship invitation',
                'friend' => [
                    'background_image' => $this->friend->background_image,
                    'first_name' => $this->friend->first_name,
                    'id' => $this->friend->id,
                    'name' => "{$this->friend->first_name} {$this->friend->last_name}",
                    'profile_image' => $this->friend->profile_image,
                ],
                'link' => '/friends/invites',
            ]);
    }

    public function testPokedNotificationReturnProperlyData(): void
    {
        $this->user->notify(new Poked($this->friend->id, 50));

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'message' => 'Poked you 50 times in a row',
                'friend' => [
                    'background_image' => $this->friend->background_image,
                    'first_name' => $this->friend->first_name,
                    'id' => $this->friend->id,
                    'name' => "{$this->friend->first_name} {$this->friend->last_name}",
                    'profile_image' => $this->friend->profile_image,
                ],
                'link' => '/friends/pokes',
            ]);
    }
}
