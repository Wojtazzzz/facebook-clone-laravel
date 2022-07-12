<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\WithFaker;
use Mmo\Faker\PicsumProvider;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();

        $this->faker->addProvider(new PicsumProvider($this->faker));
    }

    public function __invoke()
    {
        $post = Post::factory()->createOne();
    }
}
