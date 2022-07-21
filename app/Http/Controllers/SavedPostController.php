<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Saved\Post\StoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\SavedPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedPostController extends Controller
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
            ->whereRelation('stored', 'user_id', $user->id)
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
        SavedPost::create($request->validated());

        return response()->json([
            'message' => 'Post saved successfully',
        ], 201);
    }

    public function destroy()
    {
    }
}
