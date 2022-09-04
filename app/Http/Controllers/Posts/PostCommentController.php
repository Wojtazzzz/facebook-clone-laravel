<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
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

        return response()->json([
            'data' => CommentResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function store(StoreRequest $request, Post $post): JsonResponse
    {
        $comment = new Comment($request->validated() + [
            'author_id' => $request->user()->id,
        ]);

        $post->comments()->save($comment);

        return response()->json(new CommentResource($comment), 201);
    }

    public function update(UpdateRequest $request, Post $post, Comment $comment): JsonResponse
    {
        $data = $request->validated();

        $comment->update([
            'content' => $data['content'],
        ]);

        return response()->json(new CommentResource($comment));
    }

    public function destroy(Post $post, Comment $comment): Response
    {
        $this->authorize('delete', [Comment::class, $comment]);

        $comment->delete();

        return response()->noContent();
    }
}
