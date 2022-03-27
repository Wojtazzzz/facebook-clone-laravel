<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $friendsId = collect([
            ...$user->invitedFriends->pluck('id'),
            ...$user->invitedByFriends->pluck('id')
        ]);

        $posts = Post::with('author:id,first_name,last_name,profile_image,background_image')
            ->whereIn('author_id', $friendsId)
            ->latest()
            ->paginate(15, [
                'id',
                'content',
                'author_id',
                'created_at',
                'updated_at'
            ]);

        return response()->json(PostResource::collection($posts));
    }
}
