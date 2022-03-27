<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::firstWhere('last_name', 'Witas');
        $post = Post::findOrFail(124);

        return Gate::authorize('create', [Like::class, $post]);
    }
}