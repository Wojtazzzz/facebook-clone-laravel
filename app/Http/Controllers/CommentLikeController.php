<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CommentLike\DestroyRequest;
use App\Http\Requests\CommentLike\StoreRequest;
use App\Http\Resources\LikeResource;
use App\Models\Comment;
use App\Models\Like;
use App\Notifications\CommentLiked;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CommentLikeController extends Controller
{
    public function index(Comment $comment): JsonResponse
    {
        return response()->json(LikeResource::collection($comment->likes));
    }

    public function store(StoreRequest $request, Comment $comment): Response
    {
        $userId = $request->user()->id;

        $like = new Like([
            'user_id' => $userId,
        ]);

        $comment->likes()->save($like);

        $comment->author->notify(new CommentLiked($userId, $comment->id));

        return response(status: 201);
    }

    public function destroy(DestroyRequest $request, Comment $comment): Response
    {
        $comment->likes()
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->noContent();
    }
}
