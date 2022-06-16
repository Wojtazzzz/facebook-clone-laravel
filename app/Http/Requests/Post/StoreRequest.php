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
                'required_without:images',
                'string',
                'min:2',
                'max:500',
            ],

            'images' => [
                'required_without:content',
                'array',
            ],

            'images.*' => [
                'required',
                'file',
                'image',
            ],
        ];
    }
}
