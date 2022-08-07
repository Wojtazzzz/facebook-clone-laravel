<?php

declare(strict_types=1);

namespace App\Http\Requests\Friendship\Invite;

use App\Rules\FriendshipUnique;
use App\Rules\NotSelfId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'friend_id' => [
                'required',
                'integer',
                'exists:users,id',
                new NotSelfId(),
                new FriendshipUnique(),
            ],
        ];
    }
}
