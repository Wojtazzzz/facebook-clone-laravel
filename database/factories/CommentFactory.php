<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        $users = User::pluck('id');
        $posts = Post::pluck('id');

        return [
            'content' => $this->faker->text(),
            'resource' => 'POST',
            'author_id' => $this->faker->randomElement($users),
            'resource_id' => $this->faker->randomElement($posts),
        ];
    }
}
