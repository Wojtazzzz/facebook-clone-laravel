<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Friendship;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'text' => $this->faker->text(),
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Message $message) {
            $friendshipExists = Friendship::query()
                ->where([
                    'user_id' => $message->sender_id,
                    'friend_id' => $message->receiver_id,
                ])
                ->orWhere([
                    'user_id' => $message->receiver_id,
                    'friend_id' => $message->sender_id,
                ])
                ->exists();

            if ($friendshipExists) {
                return;
            }

            Friendship::create([
                'user_id' => $message->sender_id,
                'friend_id' => $message->receiver_id,
                'status' => 'CONFIRMED',
            ]);
        });
    }
}
