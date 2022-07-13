<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Saved\Post\StoreRequest;
use App\Models\SavedPost;
use Illuminate\Http\JsonResponse;

class SavedPostController extends Controller
{
    public function index()
    {
    }

    public function store(StoreRequest $request): JsonResponse
    {
        SavedPost::create($request->validated());

        return response()->json([
            'message' => 'Post saved successfully',
        ], 201);
    }

    public function destroy()
    {
    }
}
