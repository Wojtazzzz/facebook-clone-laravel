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
                Rule::unique('friendships', 'first_user')->where(function ($query) {
                    return $query->where('second_user', $this->user()->id);
                }),
                Rule::unique('friendships', 'second_user')->where(function ($query) {
                    return $query->where('first_user', $this->user()->id);
                })
            ]
        ];
    }
}
