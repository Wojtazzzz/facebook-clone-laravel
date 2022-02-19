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
        User::factory(50)->create();

        $faker = Faker::create();

        User::create([
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);
    }
}
