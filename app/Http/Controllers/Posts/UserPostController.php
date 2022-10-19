<?php

declare(strict_types=1);

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\User;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;

class UserPostController extends Controller
{
    public function index(User $user): JsonResponse
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
}
