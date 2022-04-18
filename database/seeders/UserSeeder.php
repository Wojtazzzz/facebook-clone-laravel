<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\Like;
use App\Models\Message;
use App\Models\Poke;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

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
            ->createOne([
                'first_name' => 'Marcin',
                'last_name' => 'Witas',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'profile_image' => $this->faker->imageUrl(168, 168),
                'background_image' => $this->faker->imageUrl(850, 350)
            ]);

        Friendship::factory(50, [
            'user_id' => $user->id
        ])->create();

        Friendship::factory(50, [
            'friend_id' => $user->id
        ])->create();

        Message::factory(100, [
            'sender_id' => $user->id
        ])->create();

        Message::factory(100, [
            'receiver_id' => $user->id
        ])->create();

        Poke::factory(10, [
            'initiator_id' => $user->id
        ])->create();

        Poke::factory(10, [
            'poked_id' => $user->id
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
