<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PostLike\DestroyRequest;
use App\Http\Requests\PostLike\StoreRequest;
use App\Http\Resources\LikeResource;
use App\Models\Like;
use App\Models\Post;
use App\Notifications\PostLiked;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostLikeController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        return response()->json(LikeResource::collection($post->likes));
    }

    public function store(StoreRequest $request, Post $post): Response
    {
        $userId = $request->user()->id;

        $like = new Like([
            'user_id' => $userId,
        ]);

        $post->likes()->save($like);

        $post->author->notify(new PostLiked($userId, $post->id));

        return response(status: 201);
    }

    public function destroy(DestroyRequest $request, Post $post): Response
    {
        $post->likes()
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->noContent();
    }
}
