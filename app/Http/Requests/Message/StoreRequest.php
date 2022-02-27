<?php

namespace App\Http\Requests\Message;

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
            'text' => [
                'required',
                'string',
                'max:200'
            ],
            'receiver_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn($this->user()->id)
            ]
        ];
    }
}
