<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\MarkAsReadRequest;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'paginator' => $request->user()->notifications->paginate(10)
        ]);
    }

    public function markAsRead(MarkAsReadRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();

        $request->user()
            ->notifications
            ->whereIn('id', $data['notifications'])
            ->markAsRead();

        return response(status: 201);
    }
}
