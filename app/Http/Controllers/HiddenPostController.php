<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Hidden\Post\StoreRequest;
use App\Models\HiddenPost;
use Illuminate\Http\JsonResponse;

class HiddenPostController extends Controller
{
    public function index()
    {
    }

    public function store(StoreRequest $request): JsonResponse
    {
        HiddenPost::create($request->validated());

        return response()->json([
            'message' => 'Post hidden successfully',
        ], 201);
    }

    public function destroy()
    {
    }
}
