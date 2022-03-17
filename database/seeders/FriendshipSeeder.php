<?php

namespace Database\Seeders;

use App\Models\Friendship;
use Illuminate\Database\Seeder;

class FriendshipSeeder extends Seeder
{
    public function run()
    {
        // Friendships only for root user
        Friendship::factory(50)->create();
    }
}
