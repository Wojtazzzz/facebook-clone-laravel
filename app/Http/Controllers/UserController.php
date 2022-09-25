<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\SearchHits\UserHitResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pagination = User::search($request->search)
            ->paginate(10);

        return PaginatedResponseFacade::response(UserHitResource::class, $pagination);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }
}
