<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function index(Request $request, int $resourceId): JsonResponse
    {
        // if ('posts' === $request->segment(2)) {
        //     $post = Post::findOrFail($resourceId);
        //     $comments = $post->comments;
        // } elseif ('comments' === $request->segment(2)) {
        //     $comment = Comment::findOrFail($resourceId);
        //     $comments = $comment->comments;
        // } else {
        //     // $sale = Sale::findOrFail($resourceId);
        //     // $comments = $sale->comments;
        // }

        $post = Post::findOrFail($resourceId);
        $comments = $post->comments()->withCount([
            'likes',
            'likes as is_liked' => fn (Builder $query) => $query->where('user_id', $request->user()->id),
        ]);

        // $pagination = $comments->sortByDesc('created_at')->paginate(10);
        $pagination = $comments->paginate(10);

        return response()->json([
            'data' => CommentResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function store(StoreRequest $request, int $resourceId): JsonResponse
    {
        $resource = match ($request->segment(2)) {
            default => 'POST',
            'comments' => 'COMMENT',
            'sales' => 'SALE',
        };

        Post::findOrFail($resourceId);

        $comment = Comment::create($request->validated() + [
            'resource' => $resource,
            'resource_id' => $resourceId,
            'author_id' => $request->user()->id,
        ]);

        return response()->json(new CommentResource($comment), 201);
    }

    public function update(UpdateRequest $request, int $resourceId, Comment $comment): JsonResponse
    {
        $data = $request->validated();

        $comment->update([
            'content' => $data['content'],
        ]);

        return response()->json(new CommentResource($comment));
    }

    public function destroy(int $resourceId, Comment $comment): Response
    {
        $this->authorize('delete', [Comment::class, $comment]);

        $comment->delete();

        return response()->noContent();
    }
}
