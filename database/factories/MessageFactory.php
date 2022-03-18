<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition()
    {
        $rootUserId = User::where('last_name', 'Witas')->value('id');

        return [
            'text' => $this->faker->text(20),
            'sender_id' => $rootUserId,
            'receiver_id' => $this->faker->numberBetween(1, 50),
        ];
    }
}
