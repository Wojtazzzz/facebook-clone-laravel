<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Like\DestroyRequest;
use App\Http\Requests\Like\StoreRequest;
use App\Http\Resources\LikeResource;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostLikeController extends Controller
{
    public function store(StoreRequest $request, Post $post): Response
    {
        $like = new Like([
            'user_id' => $request->user()->id,
        ]);

        $post->likes()->save($like);

        return response(status: 201);
    }

    public function destroy(DestroyRequest $request, Post $post): Response
    {
        $post->likes()
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->noContent();
    }

    public function index(Post $post): JsonResponse
    {
        return response()->json(LikeResource::collection($post->likes));
    }
}
