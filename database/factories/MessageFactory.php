<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition()
    {
        $rootUserId = User::where('last_name', 'Witas')->value('id');
        $otherUser = $this->faker->numberBetween(1, 30);

        $sender = $this->faker->randomElement([$rootUserId, $otherUser]);

        $date = $this->faker->date;

        return [
            'text' => $this->faker->text(20),
            'sender_id' => $sender,
            'receiver_id' => $sender === $rootUserId ? $otherUser : $rootUserId,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
