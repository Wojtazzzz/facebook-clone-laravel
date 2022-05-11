<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::authorize('update', [Comment::class, $this->route()->comment]);
    }

    public function rules()
    {
        return [
            'content' => [
                'required',
                'string',
                'min:2',
                'max:8000'
            ]
        ];
    }
}
