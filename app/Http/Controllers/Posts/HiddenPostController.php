<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hidden\Post\StoreRequest;
use App\Http\Resources\PostResource;
use App\Models\HiddenPost;
use App\Models\Post;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HiddenPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = Post::query()
            ->withAuthor()
            ->withStats()
            ->withIsLiked()
            ->withIsHidden()
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

        return PaginatedResponseFacade::response(PostResource::class, $pagination);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        HiddenPost::create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Post hidden successfully',
        ], 201);
    }

    public function destroy(Request $request, Post $post): Response
    {
        $user = $request->user();

        HiddenPost::query()
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->firstOrFail()
            ->delete();

        return response()->noContent();
    }
}
