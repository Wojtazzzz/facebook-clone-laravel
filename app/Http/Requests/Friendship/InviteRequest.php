<?php

namespace App\Http\Requests\Friendship;

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
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                // Cannot invite self
                Rule::notIn([$this->user()->id]),
                // Cannot invite friend
                Rule::unique('friendships', 'user_id')->where(function ($query) {
                    return $query->where('friend_id', $this->user()->id);
                }),
                Rule::unique('friendships', 'friend_id')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                })
            ]
        ];
    }
}
