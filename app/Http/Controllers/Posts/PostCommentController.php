<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class PostCommentController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        $pagination = $post->comments()
            ->withCount('likes')
            ->withIsLiked()
            ->latest()
            ->paginate(10, [
                'id',
                'content',
                'commentable_id',
                'is_edited',
                'is_liked',
                'likes_count',
                'created_at',
            ]);

        return PaginatedResponseFacade::response(CommentResource::class, $pagination);
    }

    public function store(StoreRequest $request, Post $post): JsonResponse
    {
        $comment = new Comment($request->validated() + [
            'author_id' => $request->user()->id,
        ]);

        $post->comments()->save($comment);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateRequest $request, Post $post, Comment $comment): JsonResource
    {
        $data = $request->validated();

        $comment->update([
            'content' => $data['content'],
        ]);

        return new CommentResource($comment);
    }

    public function destroy(Post $post, Comment $comment): Response
    {
        $this->authorize('delete', [Comment::class, $comment]);

        $comment->delete();

        return response()->noContent();
    }
}
