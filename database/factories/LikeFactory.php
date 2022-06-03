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
        $users = User::pluck('id');
        $posts = Post::pluck('id');

        return [
            'user_id' => $this->faker->randomElement($users),
            'post_id' => $this->faker->randomElement($posts)
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
