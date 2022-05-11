<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition()
    {
        $resource = $this->faker->randomElement(['POST', 'COMMENT']);

        return [
            'content' => $this->faker->text(),
            'resource' => $resource,
            'author_id' => $this->faker->numberBetween(1, User::count()),
            'resource_id' => $resource === 'POST' 
                ? $this->faker->numberBetween(1, Post::count()) 
                : $this->faker->numberBetween(1, Comment::count() === 0 
                    ? 1
                    : Comment::count())
        ];
    }
}
