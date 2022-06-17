<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function index(Request $request, int $resourceId): JsonResponse
    {
        if ('posts' === $request->segment(2)) {
            $post = Post::findOrFail($resourceId);
            $comments = $post->comments;
        } elseif ('comments' === $request->segment(2)) {
            $comment = Comment::findOrFail($resourceId);
            $comments = $comment->comments;
        } else {
            // $sale = Sale::findOrFail($resourceId);
            // $comments = $sale->comments;
        }

        $comments = $comments->sortByDesc('created_at')->paginate(10);

        return response()->json(CommentResource::collection($comments));
    }

    public function store(StoreRequest $request, int $resourceId): JsonResponse
    {
        $resource = match ($request->segment(2)) {
            default => 'POST',
            'comments' => 'COMMENT',
            'sales' => 'SALE',
        };

        $comment = Comment::create($request->validated() + [
            'resource' => $resource,
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
