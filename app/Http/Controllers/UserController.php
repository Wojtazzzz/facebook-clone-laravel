<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\SearchHits\UserHitResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pagination = User::search($request->search)
            ->paginate(10);

        return response()->json([
            'data' => UserHitResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }
}
