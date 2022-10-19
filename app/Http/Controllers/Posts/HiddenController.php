<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hidden\Post\StoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Hidden;
use App\Models\Post;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HiddenController extends Controller
{
    public function index(): JsonResponse
    {
        $pagination = Post::query()
            ->withAuthor()
            ->withStats()
            ->withIsLiked()
            ->withIsHidden()
            ->whichHidden()
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

    public function store(StoreRequest $request): Response
    {
        Hidden::create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return response(status: 201);
    }

    public function destroy(Request $request, Post $post): Response
    {
        $user = $request->user();

        Hidden::query()
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->firstOrFail()
            ->delete();

        return response()->noContent();
    }
}
