<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class LikeFactory extends Factory
{
    public function definition()
    {
        $usersCount = User::count();
        $postsCount = Post::count();

        return [
            'user_id' => $this->faker->numberBetween(1, $usersCount),
            'post_id' => $this->faker->numberBetween(1, $postsCount),
            'created_at' => $this->faker->date()
        ];
    }

    public function randomUser(Collection $usersIds)
    {
        $fakerInstance = $this->faker->unique(true);

        return $this->state(fn () => [
            'user_id' => $fakerInstance->randomElement($usersIds)
        ]);
    }
}
