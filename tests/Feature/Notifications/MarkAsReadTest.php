<?php

namespace Tests\Feature\Notifications;

use App\Models\Notification;
use App\Models\User;
use Tests\TestCase;

class MarkAsReadTest extends TestCase
{
    private User $user;

    private string $notificationsRoute;

    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->notificationsRoute = route('api.notifications.markAsRead');
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->putJson($this->notificationsRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->putJson($this->notificationsRoute);
        $response->assertOk();
    }

    public function testCanMarkAsReadAllNotifications()
    {
        Notification::factory(50)->create();

        $response = $this->actingAs($this->user)->putJson($this->notificationsRoute);

        $response->assertOk();
        $this->assertDatabaseMissing($this->notificationsTable, [
            'read_at' => null,
        ]);
    }

    public function testWorksWhenUserHasNotNotifications()
    {
        $response = $this->actingAs($this->user)->putJson($this->notificationsRoute);
        $response->assertOk();
    }
}
