<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Posts\CombinedPostResource;
use App\Http\Resources\Posts\OwnPostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $authors = collect([
            $user,
            ...$user->invitedFriends,
            ...$user->invitedByFriends,
        ]);

        // @todo $authors should come from single relation
        $authors = User::find($authors->pluck('id'));

        $pagination = Post::query()
            ->withAuthor()
            ->withStats()
            ->withIsLiked()
            ->fromAuthors($authors)
            ->whichNotHidden()
            ->latest()
            ->paginate(10, [
                'id',
                'content',
                'images',
                'author_id',
                'commenting',
                'created_at',
                'updated_at',
            ]);

        return response()->json([
            'data' => CombinedPostResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function userPosts(Request $request, User $user): JsonResponse
    {
        $pagination = Post::query()
            ->withStats()
            ->withIsLiked()
            ->fromAuthors($user)
            ->whichNotHidden()
            ->latest()
            ->paginate(10, [
                'id',
                'content',
                'images',
                'author_id',
                'created_at',
                'updated_at',
            ]);

        return response()->json([
            'data' => CombinedPostResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');

                $paths[] = str_replace('public', '', $path);
            }
        }

        $post = Post::create([
            'content' => $request->validated('content'),
            'images' => $paths,
            'author_id' => $request->user()->id,
        ]);

        return response()->json([
            'data' => new OwnPostResource($post),
            'message' => 'Post was created',
        ], 201);
    }

    public function destroy(Post $post): Response
    {
        $this->authorize('delete', [Post::class, $post]);

        $post->delete();

        return response()->noContent();
    }

    public function turnOffComments(Post $post): Response
    {
        $this->authorize('turnOffComments', [Post::class, $post]);

        $post->update([
            'commenting' => false,
        ]);

        return response(status: 200);
    }

    public function turnOnComments(Post $post): Response
    {
        $this->authorize('turnOnComments', [Post::class, $post]);

        $post->update([
            'commenting' => true,
        ]);

        return response(status: 200);
    }
}
