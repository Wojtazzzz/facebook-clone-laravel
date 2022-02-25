<?php

use App\Http\Controllers\Api\MessageController;
use App\Models\Message;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $sendedMessages = Message::where([
        ['sender_id', 51],
        ['receiver_id', 1]
    ])->get();

    $receivedMessages = Message::where([
        ['sender_id', 1],
        ['receiver_id', 51]
    ])->get();

    
    $messages = $sendedMessages->merge($receivedMessages);

    return response()->json([
        'paginator' => paginate($messages->sortBy('created_at'))
    ]);
});

require __DIR__.'/auth.php';

function paginate($items, $perPage = 15, $page = null, $options = []): LengthAwarePaginator
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);

    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}