<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Like\StoreRequest;
use App\Http\Resources\LikeResource;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(StoreRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        Like::create($data + [
            'user_id' => $user->id,
        ]);

        $post = Post::with('likes')->findOrFail($data['post_id']);

        return response()->json([
            'data'    => new LikeResource($post->likes->count()),
            'message' => 'Post was liked',
        ], 201);
    }

    public function destroy(Request $request, Post $post)
    {
        Like::firstWhere([
            ['user_id', $request->user()->id],
            ['post_id', $post->id],
        ])->delete();

        $post = Post::with('likes')->findOrFail($post->id);

        return response()->json([
            'data'    => new LikeResource($post->likes->count()),
            'message' => 'Post was unliked',
        ], 201);
    }
}
