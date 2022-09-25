<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Saved\Post\StoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\SavedPost;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SavedPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = Post::query()
            ->withAuthor()
            ->withStats()
            ->withIsLiked()
            ->withIsSaved()
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

        return PaginatedResponseFacade::response(PostResource::class, $pagination);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        SavedPost::create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Post saved successfully',
        ], 201);
    }

    public function destroy(Request $request, Post $post): Response
    {
        $user = $request->user();

        SavedPost::query()
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->firstOrFail()
            ->delete();

        return response()->noContent();
    }
}
