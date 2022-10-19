<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Response;

class PostCommentingController extends Controller
{
    public function update(Post $post): Response
    {
        $this->authorize('commenting', [Post::class, $post]);

        $post->update([
            'commenting' => !$post->commenting,
        ]);

        return response(status: 200);
    }
}
