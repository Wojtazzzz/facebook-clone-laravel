<?php

namespace App\Http\Requests\Poke;

use App\Rules\IsFriend;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
                // Cannot poke self
                Rule::notIn([$this->user()->id]),
                // Check user is your friend
                new IsFriend()
            ]
        ];
    }
}
