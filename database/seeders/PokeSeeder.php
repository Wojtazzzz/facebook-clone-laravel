<?php

declare(strict_types=1);

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

    public function run(User $user, int $count): void
    {
        $friends = collect([
            ...$user->invitedFriends,
            ...$user->invitedByFriends,
        ])->pluck('id');

        $faker = $this->faker->unique();

        Poke::factory($count)
            ->state(fn () => [
                'user_id' => $user->id,
                'friend_id' => $faker->randomElement($friends),
            ])->create([
                'latest_initiator_id' => $user->id,
            ]);

        for ($i = 0; $i < $count; ++$i) {
            $friendId = $faker->randomElement($friends);

            Poke::factory()
                ->createOne([
                    'user_id' => $friendId,
                    'friend_id' => $user->id,
                    'latest_initiator_id' => $friendId,
                ]);
        }
    }
}
