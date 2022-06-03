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

        Poke::factory($count)->create([
            'initiator_id' => $user->id,
            'poked_id' => fn () => $faker->randomElement($users->except($user->id))
        ]);

        Poke::factory($count)->create([
            'initiator_id' => fn () => $faker->randomElement($users->except($user->id)),
            'poked_id' => $user->id
        ]);
    }
}
