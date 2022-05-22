<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(User $user, int $count)
    {
        Message::factory(ceil($count / 2))->create([
            'sender_id' => $user->id
        ]);

        Message::factory(floor($count / 2))->create([
            'receiver_id' => $user->id
        ]);
    }
}