<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File;

class UpdateRequest extends FormRequest
{
    public function authorize(): Response
    {
        return Gate::authorize('update', [Post::class, $this->route()->post]);
    }

    public function rules(): array
    {
        return [
            'content' => [
                'nullable',
                'string',
                'min:2',
                'max:1000',
            ],

            'images' => [
                'nullable',
                'array',
            ],

            'images.*' => [
                'required',
                File::image(),
            ],

            'imagesToDelete' => [
                'nullable',
                'array',
            ],

            'imagesToDelete.*' => [
                'required',
                'string',
            ],
        ];
    }
}
