<?php

declare(strict_types=1);

namespace App\Http\Requests\Comment;

use App\Models\Post;
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
                'required',
                'string',
                'min:2',
                'max:1000',
            ],

            'resource_id' => [
                'required',
                'numeric',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $resource = match ($this->segment(2)) {
                default => Post::find($this->input('resource_id')),
                'comments' => Post::find($this->input('resource_id')),
                // 'sales' => Sale::find($this->input('resource_id')),
            };

            if (!(bool) $resource) {
                $validator->errors()->add('resource_id', 'You are trying to comment unrecognized resource');
            }
        });
    }
}
