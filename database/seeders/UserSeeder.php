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
use Mmo\Faker\PicsumProvider;

class UserSeeder extends Seeder
{
    use WithFaker;

    private int $postsCount = 10;
    private int $messagesCount = 500;

    public function __construct()
    {
        $this->setUpFaker();
        $this->faker->addProvider(new PicsumProvider($this->faker));
    }

    public function run()
    {
        $user = User::factory()
            ->createOne([
                'first_name' => 'Marcin',
                'last_name' => 'Witas',
                'email' => 'marcin.witas72@gmail.com',
                'password' => Hash::make('password'),
                'profile_image' => $this->faker->picsumStaticRandomUrl(168, 168),
                'background_image' => $this->faker->picsumStaticRandomUrl(850, 350)
            ]);

        // $user = User::firstWhere('last_name', 'Witas');

        $this->call(PostSeeder::class, false, [
            'user' => $user,
            'count' => $this->postsCount,
            'commentsCount' => rand(1, 3)
        ]);

        $this->call(MessageSeeder::class, false, [
            'user' => $user,
            'count' => $this->messagesCount
        ]);

        // Friendship::factory(50, [
        //     'user_id' => $user->id
        // ])->create();

        // Friendship::factory(50, [
        //     'friend_id' => $user->id
        // ])->create();

        // Message::factory(100, [
        //     'sender_id' => $user->id
        // ])->create();

        // Message::factory(100, [
        //     'receiver_id' => $user->id
        // ])->create();

        // Poke::factory(10, [
        //     'initiator_id' => $user->id
        // ])->create();

        // Poke::factory(10, [
        //     'poked_id' => $user->id
        // ])->create();
    }
}
