<?php

namespace App\Http\Requests\Friendship;

use App\Rules\FriendshipUnique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InviteRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'friend_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([$this->user()->id]),
                new FriendshipUnique(),
            ],
        ];
    }
}
