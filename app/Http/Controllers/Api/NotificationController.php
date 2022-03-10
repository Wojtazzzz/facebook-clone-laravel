<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\MarkAsReadRequest;
use App\Traits\CollectionPaginate;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    use CollectionPaginate;

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'paginator' => $this->paginate($request->user()->notifications, 10)
        ]);
    }

    public function markAsRead(MarkAsReadRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();

        $request->user()->notifications->whereIn('id', $data['notifications'])->markAsRead();

        return response(status: 201);
    }
}
