<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\Message;
use App\Models\Poke;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::truncate();
        Friendship::truncate();
        Poke::truncate();
        DB::table('notifications')->truncate();
        Message::truncate();

        $this->call([
            UserSeeder::class,
            FriendshipSeeder::class,
            PokeSeeder::class,
            NotificationSeeder::class,
            MessageSeeder::class,
            PostSeeder::class,
        ]);
    }
}
