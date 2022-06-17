<?php

namespace App\Http\Requests\Friendship;

use App\Rules\RequestReceived;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AcceptRequest extends FormRequest
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
                new RequestReceived(),
            ],
        ];
    }
}
