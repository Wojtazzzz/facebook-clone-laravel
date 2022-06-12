<?php

namespace Database\Seeders;

use App\Models\Poke;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class PokeSeeder extends Seeder
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function run(User $user, int $count)
    {
        $users = User::pluck('id');

        $faker = $this->faker->unique();

        $friendId = fn () => $faker->randomElement($users->except($user->id));

        Poke::factory($count)->create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'latest_initiator_id' => $user->id,
        ]);

        Poke::factory($count)->create([
            'user_id' => $friendId,
            'friend_id' => $user->id,
            'latest_initiator_id' => $friendId,
        ]);
    }
}
