<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition()
    {
        $users = User::pluck('id');

        return [
            'text' => $this->faker->text(),
            'sender_id' => $this->faker->randomElement($users),
            'receiver_id' => $this->faker->randomElement($users),
        ];
    }
}
