<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pagination = $request->user()->notifications->paginate(15);

        return response()->json([
            'data' => NotificationResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function markAsRead(Request $request): Response
    {
        $notifications = $request->user()->notifications;
        $notifications->markAsRead();

        return response()->noContent();
    }
}
