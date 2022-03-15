<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            FriendshipSeeder::class,
            PokeSeeder::class,
            MessageSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
