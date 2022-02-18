<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Seeder;

class FriendshipSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('last_name', 'Witas')->first(['id']);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 1,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 2,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 3,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 4,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 5,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 6,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 7,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 8,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 9,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 10,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 11,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);

        Friendship::create([
            'first_user' => $user->id,
            'second_user' => 12,
            'acted_user' => $user->id,
            'status' => 'confirmed'
        ]);
    }
}
