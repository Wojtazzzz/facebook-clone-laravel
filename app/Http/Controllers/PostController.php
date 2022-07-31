<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Posts\CombinedPostResource;
use App\Http\Resources\Posts\OwnPostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $authorsId = collect([
            $user->id,
            ...$user->invitedFriends->pluck('id'),
            ...$user->invitedByFriends->pluck('id'),
        ]);

        $posts = Post::query()
            ->with('author:id,first_name,last_name,profile_image,background_image')
            ->withCount([
                'likes',
                'comments' => fn (Builder $query) => $query->where('resource', 'POST'),
                'likes as isLiked' => fn (Builder $query) => $query->where('user_id', $user->id),
            ])
            ->whereIn('author_id', $authorsId)
            ->notHidden()
            ->latest()
            ->paginate(10, [
                'id',
                'content',
                'images',
                'author_id',
                'created_at',
                'updated_at',
            ]);

        return response()->json(CombinedPostResource::collection($posts));
    }

    public function userPosts(Request $request, User $user): JsonResponse
    {
        $posts = Post::query()
            ->withCount([
                'likes',
                'comments' => fn (Builder $query) => $query->where('resource', 'POST'),
                'likes as isLiked' => fn (Builder $query) => $query->where('user_id', $request->user()->id),
            ])
            ->whereRelation('author', 'author_id', $user->id)
            ->notHidden()
            ->latest()
            ->paginate(10, [
                'id',
                'content',
                'images',
                'author_id',
                'created_at',
                'updated_at',
            ]);

        return response()->json(CombinedPostResource::collection($posts));
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
            // @todo remove after BE tests
            // 'content' => $request->validated('content', null),
            'content' => $request->validated('content'),
            'images' => $paths,
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
}
