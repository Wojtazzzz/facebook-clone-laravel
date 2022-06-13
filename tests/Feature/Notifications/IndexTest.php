<?php

namespace Tests\Feature\Notifications;

use App\Models\Notification;
use App\Models\User;
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

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->getJson($this->notificationsRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk();
    }

    public function testReturnProperlyNotificationsNumber()
    {
        Notification::factory(8)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk()
            ->assertJsonCount(8);
    }

    public function testReturnMaxTenNotifications()
    {
        Notification::factory(12)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute);
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchPaginatedDataFromSecondPage()
    {
        Notification::factory(16)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute.'?page=2');
        $response->assertOk()
            ->assertJsonCount(6);
    }

    public function testReturnEmptyListWhenNoNotifications()
    {
        $response = $this->actingAs($this->user)->getJson($this->notificationsRoute.'?page=2');
        $response->assertOk()
            ->assertJsonCount(0);
    }
}
