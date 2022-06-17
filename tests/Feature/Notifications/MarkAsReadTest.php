<?php

declare(strict_types=1);

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

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->putJson($this->notificationsRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->notificationsRoute);
        $response->assertOk();
    }

    public function testCanMarkAsReadAllNotifications(): void
    {
        Notification::factory(50)->create();

        $response = $this->actingAs($this->user)->putJson($this->notificationsRoute);

        $response->assertOk();
        $this->assertDatabaseMissing($this->notificationsTable, [
            'read_at' => null,
        ]);
    }

    public function testWorksWhenUserHasNotNotifications(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->notificationsRoute);
        $response->assertOk();
    }
}
