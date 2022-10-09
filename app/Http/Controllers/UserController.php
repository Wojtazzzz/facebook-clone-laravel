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
        $pagination = User::query()
            ->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$request->search%"])
            ->paginate(10, [
                'id',
                'first_name',
                'last_name',
                'profile_image',
            ]);

        return PaginatedResponseFacade::response(UserHitResource::class, $pagination);
    }

    public function user(Request $request)//: JsonResponse
    {
        session()->put('dupa', 'DUPA');

        return session()->all();

        return response()->json(new UserResource($request->user()));
    }
}
