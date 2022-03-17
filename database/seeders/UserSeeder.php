<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        User::factory(100)->create();

        // Root user
        User::create([
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);
    }
}
