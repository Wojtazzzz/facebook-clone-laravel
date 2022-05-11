<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();    
    }

    public function __invoke()
    {
        $class = Post::class;
        
        return $class;
    }
}
