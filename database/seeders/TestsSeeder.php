<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class TestsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        User::truncate();
        Friendship::truncate();

        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'password' => Hash::make('test'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);
    }
}
