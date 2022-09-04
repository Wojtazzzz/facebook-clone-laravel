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
        return [
            'content' => $this->faker->text,
            'commentable_type' => Post::class,
            'commentable_id' => Post::factory(),
            'author_id' => User::factory(),
        ];
    }

    public function forPost(int $postId = null): static
    {
        return $this->state(function (array $attributes) use ($postId) {
            return [
                'commentable_type' => Post::class,
                'commentable_id' => $postId ?? Post::factory(),
            ];
        });
    }
}
