<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'content' => [
                'nullable',
                'required_if:images,""',
                'string',
                'min:2',
                'max:63206'
            ],

            'images' => [
                'required_if:content,""',
                'array',
            ],

            'images.*' => [
                'required',
                'file',
                'image'
            ]
        ];
    }
}
