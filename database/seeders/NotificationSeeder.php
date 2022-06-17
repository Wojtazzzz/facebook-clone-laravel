<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(User $user, int $count): void
    {
        Notification::factory($count)->create([
            'notifiable_id' => $user->id,
        ]);
    }
}
