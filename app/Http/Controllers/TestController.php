<?php

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends Controller
{
    private array $columns = ['users.id', 'first_name', 'last_name', 'profile_image', 'messages.created_at', 'messages.text as message']; 

    public function __invoke()
    {
        $user = User::findOrFail(51);

        $user->makeVisible(['created_at']);

        $user->load([
            'messages' => function ($query) use ($user) {
                $query
                    ->with(['messages' => function ($subQuery) use ($user) {
                        $subQuery
                            ->where('receiver_id', $user->id)
                            ->latest('messages.created_at')
                            ->limit(1)
                            ->select($this->columns)
                            ;
                    }])
                    ->latest('messages.created_at')
                    ->select($this->columns)
                    ;
            }
        ]);

        $messages = $user->messages->unique()->map(function ($item) {
            $userMessage = $item;
            $friendMessage = $item->messages[0] ?? null;

            if (!$friendMessage || $userMessage->created_at > $friendMessage->created_at) {
                unset($userMessage->messages);

                $model = $userMessage;
            }
            
            $model = $model ?? $friendMessage;

            return $model->makeVisible(['created_at']);
        });

        return $messages;
    }
}