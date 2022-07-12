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
        $notifications = $request->user()->notifications->paginate(10);

        return response()->json(NotificationResource::collection($notifications));
    }

    public function markAsRead(Request $request): Response
    {
        $notifications = $request->user()->notifications;
        $notifications->markAsRead();

        return response()->noContent();
    }
}
