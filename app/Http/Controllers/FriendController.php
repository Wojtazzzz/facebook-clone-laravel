<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\FriendResource;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FriendController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = $user->friends->paginate(10);

        return response()->json([
            'data' => FriendResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function destroy(Request $request, User $user): Response
    {
        Friendship::query()
            ->relation($request->user()->id, $user->id)
            ->firstOrFail()
            ->delete();

        return response()->noContent();
    }
}
