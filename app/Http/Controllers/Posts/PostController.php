<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = Post::query()
            ->withAuthor()
            ->withStats()
            ->withIsLiked()
            ->withIsSaved()
            ->withIsHidden()
            ->fromUserAndFriends($user)
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

        return PaginatedResponseFacade::response(PostResource::class, $pagination);
    }

    public function userPosts(User $user): JsonResponse
    {
        $pagination = $user->posts()
            ->withStats()
            ->withIsLiked()
            ->withIsSaved()
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

        return PaginatedResponseFacade::response(PostResource::class, $pagination);
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

        $post = $request->user()->posts()->create([
            'content' => $request->validated('content'),
            'images' => $paths,
        ]);

        return response()->json([
            'data' => new PostResource($post),
            'message' => 'Post was created',
        ], 201);
    }

    public function update(Post $post, UpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $paths = collect($post->images);

        if (isset($data['imagesToDelete'])) {
            $paths = $paths->diff($data['imagesToDelete']);

            Storage::disk('public')->delete($data['imagesToDelete']);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');

                $paths->push(str_replace('public', '', $path));
            }
        }

        $post->update([
            'content' => $request->validated('content', $post->content),
            'images' => $paths,
        ]);

        return response()->json();
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
