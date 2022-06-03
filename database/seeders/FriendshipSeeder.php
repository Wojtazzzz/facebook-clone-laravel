<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Seeder;

class FriendshipSeeder extends Seeder
{
    public function run(User $user, int $count)
    {
        Friendship::factory($count)->create([
            'user_id' => $user->id
        ]);

        Friendship::factory($count)->create([
            'friend_id' => $user->id
        ]);
    }
}
