<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications->paginate(10);

        return response()->json(NotificationResource::collection($notifications));
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications;

        $notifications->markAsRead();

        return response()->json([
            'data' => NotificationResource::collection($notifications),
            'message' => 'All notifications marked as read',
        ]);
    }
}
