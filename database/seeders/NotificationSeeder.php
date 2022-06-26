<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\FriendshipRequestAccepted;
use App\Notifications\FriendshipRequestSent;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class NotificationSeeder extends Seeder
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function run(User $user, int $count): void
    {
        $faker = $this->faker->unique();
        $users = User::whereNot('id', $user->id)->pluck('id');

        for ($i = 0; $i < $count; ++$i) {
            $user->notify(new FriendshipRequestSent($faker->randomElement($users)));
            $user->notify(new FriendshipRequestAccepted($faker->randomElement($users)));
        }
    }
}
