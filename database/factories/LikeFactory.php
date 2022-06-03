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
<<<<<<< HEAD
        $users = User::pluck('id');
        $posts = Post::pluck('id');

        return [
            'user_id' => $this->faker->randomElement($users),
            'post_id' => $this->faker->randomElement($posts)
=======
        $usersCount = User::count();
        $postsCount = Post::count();

        return [
            'user_id' => $this->faker->numberBetween(1, $usersCount),
            'post_id' => $this->faker->numberBetween(1, $postsCount),
            'created_at' => $this->faker->date()
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
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
