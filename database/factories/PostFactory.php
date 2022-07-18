<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content' => $this->faker->text,
            'images' => $this->getRandomImages(),
            'author_id' => User::factory()->createOne()->id,
        ];
    }

    private function getRandomImages(): array
    {
        return $this->faker->randomElement([
            [],
            [$this->faker->picsumStaticRandomUrl(850, 350)],
            [$this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350)],
            [$this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350)],
            [$this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350)],
        ]);
    }

    public function friendsAuthors(int $userId): static
    {
        return $this->state(function (array $attributes) use ($userId) {
            Friendship::factory()->createOne([
                'user_id' => $userId,
                'friend_id' => $attributes['author_id'],
                'status' => FriendshipStatus::CONFIRMED,
            ]);

            return [];
        });
    }
}
