<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    public function definition(): array
    {
        $post = Post::factory()->createOne();

        return [
            'user_id' => User::factory(),
            'likeable_id' => $post->id,
            'likeable_type' => Post::class,
        ];
    }
}
