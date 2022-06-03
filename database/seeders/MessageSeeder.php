<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(User $user, int $count)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        Message::factory($count)->create([
            'sender_id' => $user->id
        ]);

        Message::factory($count)->create([
            'receiver_id' => $user->id
        ]);
    }
}
=======
=======
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
        Message::factory(ceil($count / 2))->create([
            'sender_id' => $user->id
        ]);

        Message::factory(floor($count / 2))->create([
            'receiver_id' => $user->id
        ]);
    }
<<<<<<< HEAD
}
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
=======
}
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
