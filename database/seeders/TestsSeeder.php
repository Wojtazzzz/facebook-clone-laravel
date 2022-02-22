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

        $userInvitator = User::create([
            'first_name' => 'User',
            'last_name' => 'Invitator',
            'email' => 'invitator@test.com',
            'password' => Hash::make('test'),
            'profile_image' => $faker->imageUrl(168, 168),
            'background_image' => $faker->imageUrl(850, 350)
        ]);

        Friendship::create([
            'first_user' => $userInvitator->id,
            'second_user' => $testUser->id,
            'acted_user' => $userInvitator->id,
            'status' => 'pending'
        ]);
    }
}
