<?php

namespace App\Http\Requests\Comment;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::authorize('create', Comment::class);
    }

    public function rules()
    {
        return [
            'content' => [
                'required',
                'string',
                'min:2',
                'max:8000'
            ],

            'resource_id' => [
                'required',
                'numeric'
            ]
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $resource = match ($this->segment(2)) {
                default => Post::find($this->input('resource_id')),
                'comments' => Post::find($this->input('resource_id')),
                // 'sales' => Sale::find($this->input('resource_id')),
            };

            if (!!!$resource) {
                $validator->errors()->add('resource_id', 'You are trying to comment unrecognized resource');
            }
        });
    }
}
