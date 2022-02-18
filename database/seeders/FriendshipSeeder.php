<?php

namespace Database\Seeders;

use App\Models\Friendship;
use Illuminate\Database\Seeder;

class FriendshipSeeder extends Seeder
{
    public function run()
    {
        Friendship::factory(20)->create();
    }
}
