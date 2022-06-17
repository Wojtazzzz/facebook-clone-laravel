<?php

declare(strict_types=1);

namespace App\Http\Requests\Comment;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    public function authorize(): Response
    {
        return Gate::authorize('update', [Comment::class, $this->route()->comment]);
    }

    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'min:2',
                'max:1000',
            ],
        ];
    }
}
