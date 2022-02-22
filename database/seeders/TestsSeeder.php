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

        $testUser = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'password' => Hash::make('test'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);

        $friendUser = User::create([
            'first_name' => 'Friend',
            'last_name' => 'User',
            'email' => 'friend@test.com',
            'password' => Hash::make('test'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);

        Friendship::create([
            'first_user' => $testUser->id,
            'second_user' => $friendUser->id,
            'acted_user' => $testUser->id,
            'status' => 'confirmed'
        ]);

        User::create([
            'first_name' => 'UserTo',
            'last_name' => 'Invite',
            'email' => 'invite@test.com',
            'password' => Hash::make('test'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);

        $userToAccept = User::create([
            'first_name' => 'UserTo',
            'last_name' => 'Accept',
            'email' => 'accept@test.com',
            'password' => Hash::make('test'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);

        Friendship::create([
            'first_user' => $userToAccept->id,
            'second_user' => $testUser->id,
            'acted_user' => $userToAccept->id,
            'status' => 'pending'
        ]);

        $userToReject = User::create([
            'first_name' => 'UserTo',
            'last_name' => 'Reject',
            'email' => 'reject@test.com',
            'password' => Hash::make('test'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);

        Friendship::create([
            'first_user' => $userToReject->id,
            'second_user' => $testUser->id,
            'acted_user' => $userToReject->id,
            'status' => 'pending'
        ]);
    }
}
