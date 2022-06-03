<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(User $user, int $count)
    {
        Message::factory($count)->create([
            'sender_id' => $user->id
        ]);

        Message::factory($count)->create([
            'receiver_id' => $user->id
        ]);
    }
}