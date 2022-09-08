<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Bezhanov\Faker\Provider\Educator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mmo\Faker\PicsumProvider;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();

        $this->faker->addProvider(new PicsumProvider($this->faker));
        $this->faker->addProvider(new Educator($this->faker));
    }

    public function __invoke(Request $request)
    {
        $post = Post::firstWhere('content', 'Siema');

        $img = Storage::disk('public')->get($post->images[0]);

        dd(var_dump($img));

        return response()->json();
    }
}
