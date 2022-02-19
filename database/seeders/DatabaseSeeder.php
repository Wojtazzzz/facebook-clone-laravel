<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{w
    public function run()
    {
        $this->call([
            UserSeeder::class,
            FriendshipSeeder::class
        ]);
    }
}
