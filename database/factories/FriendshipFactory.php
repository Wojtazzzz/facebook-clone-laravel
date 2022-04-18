<?php

namespace Database\Factories;

use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipInvitationAccepted;
use App\Notifications\FriendshipInvitationSended;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendshipFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, User::count()),
            'friend_id' => $this->faker->numberBetween(1, User::count()),
            'status' => $this->faker->randomElement(['CONFIRMED', 'PENDING'])
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Friendship $friendship) {
            while ($friendship->user_id === $friendship->friend_id) {
                $friendship->friend_id = $this->faker->numberBetween(1, User::count());
            }

        })->afterCreating(function (Friendship $friendship) {
            $sender = User::findOrFail($friendship->user_id);
            $receiver = User::findOrFail($friendship->friend_id);

            if ($friendship->status === 'CONFIRMED') {
                $sender->notify(new FriendshipInvitationAccepted($receiver));
                
            } elseif ($friendship->status === 'PENDING') {
                $receiver->notify(new FriendshipInvitationSended($sender));
            }
        });
    }
}
