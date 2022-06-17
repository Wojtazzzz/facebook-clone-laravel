<?php

namespace App\Http\Requests\Friendship;

use App\Rules\Friend;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyRequest extends FormRequest
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
                new Friend(),
            ],
        ];
    }
}
