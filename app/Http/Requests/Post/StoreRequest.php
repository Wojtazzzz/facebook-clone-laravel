<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'content' => [
                'nullable',
                'required_without:images',
                'string',
                'min:2',
                'max:500',
            ],

            'images' => [
                'nullable',
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
