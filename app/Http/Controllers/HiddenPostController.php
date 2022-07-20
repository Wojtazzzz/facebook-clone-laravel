<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Hidden\Post\StoreRequest;
use App\Http\Resources\PostResource;
use App\Models\HiddenPost;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HiddenPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $posts = Post::query()
            ->with('author:id,first_name,last_name,profile_image,background_image')
            ->withCount([
                'likes',
                'comments' => fn (Builder $query) => $query->where('resource', 'POST'),
                'likes as isLiked' => fn (Builder $query) => $query->where('user_id', $user->id),
            ])
            ->whereRelation('hidden', 'user_id', $user->id)
            ->latest()
            ->paginate(10, [
                'id',
                'content',
                'images',
                'author_id',
                'created_at',
                'updated_at',
            ]);

        return response()->json(PostResource::collection($posts));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        HiddenPost::create($request->validated());

        return response()->json([
            'message' => 'Post hidden successfully',
        ], 201);
    }

    public function destroy()
    {
    }
}
