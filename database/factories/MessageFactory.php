<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FriendshipStatus;
use App\Enums\MessageStatus;
use App\Models\Friendship;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        $status = [
            MessageStatus::SENDING,
            MessageStatus::DELIVERED,
            MessageStatus::READ,
        ];

        $status = $this->faker->randomElement($status);

        return [
            'text' => $this->faker->text,
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'status' => $status,
            'read_at' => $status === MessageStatus::READ ? now() : null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Message $message) {
            $friendshipExists = Friendship::query()
                ->relation($message->sender_id, $message->receiver_id)
                ->exists();

            if ($friendshipExists) {
                return;
            }

            Friendship::create([
                'user_id' => $message->sender_id,
                'friend_id' => $message->receiver_id,
                'status' => FriendshipStatus::CONFIRMED,
            ]);
        });
    }
}
