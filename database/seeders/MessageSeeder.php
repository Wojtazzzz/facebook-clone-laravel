<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(User $user, int $count): void
    {
        Message::factory($count)->create([
            'sender_id' => $user->id,
        ]);

        Message::factory($count)->create([
            'receiver_id' => $user->id,
        ]);
    }
}
