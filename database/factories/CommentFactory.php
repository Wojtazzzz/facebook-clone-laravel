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
            'resource' => 'POST',
            'author_id' => User::factory(),
            'resource_id' => Post::factory(),
        ];
    }
}
