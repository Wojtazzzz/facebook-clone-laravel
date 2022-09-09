<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = $user->friends->paginate(20);

        return response()->json([
            'data' => ContactResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }
}
