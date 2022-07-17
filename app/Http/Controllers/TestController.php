<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\WithFaker;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function __invoke()
    {
        $post = Post::factory()->createOne();
    }
}
