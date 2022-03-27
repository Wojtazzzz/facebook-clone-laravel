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

        $like = Like::create($request->validated() + [
            'user_id' => $user->id
        ]);

        return response()->json([
            'data' => new LikeResource($like),
            'message' => 'Post was liked' 
        ], 201);
    }

    public function destroy(Request $request, Post $post)
    {
        Like::firstWhere([
            ['user_id', $request->user()->id],
            ['post_id', $post->id],
        ])->delete();

        return response()->json([
            'message' => 'Post was unliked' 
        ], 201);
    }
}
