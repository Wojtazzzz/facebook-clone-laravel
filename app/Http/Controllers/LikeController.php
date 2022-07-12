<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Like\StoreRequest;
use App\Http\Resources\LikeResource;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        Like::create($data);

        $post = Post::with('likes')->findOrFail($data['post_id']);

        return response()->json([
            'data' => new LikeResource($post->likes->count()),
            'message' => 'Post was liked',
        ], 201);
    }

    public function destroy(Post $post): JsonResponse
    {
        Like::query()
            ->userLike($post)
            ->firstOrFail()
            ->delete();

        return response()->json([
            'data' => new LikeResource($post->likes->count()),
            'message' => 'Post was unliked',
        ]);
    }
}
