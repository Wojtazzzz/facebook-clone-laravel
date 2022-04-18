<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class UserSeeder extends Seeder
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function run()
    {
        $user = User::factory()
            ->has(Post::factory(20)
                ->has(Like::factory(10))
            )
            ->createOne();
        
        Friendship::factory(50, [
            'user_id' => $user->id
        ])->create();

        Friendship::factory(50, [
            'friend_id' => $user->id
        ])->create();
    }

    private function numberFromRangeWithNot(int $min, int $max, int $exception): int
    {
        do {
            $randomNumber = $this->faker()->numberBetween($min, $max);

        } while ($randomNumber === $exception);

        return $randomNumber;
    }
}
