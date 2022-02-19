<?php

namespace App\Http\Requests\Friendship;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AcceptRequest extends FormRequest
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
                Rule::exists('friendships', 'acted_user')->where(function ($query) {
                    return $query->where('second_user', $this->user()->id);
                })
            ]
        ];
    }
}
