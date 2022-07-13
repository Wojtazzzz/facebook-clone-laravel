<?php

declare(strict_types=1);

namespace App\Http\Requests\Saved\Post;

use App\Rules\NotOwnPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'post_id' => [
                'required',
                'integer',
                'exists:posts,id',
                Rule::unique('saved_posts', 'post_id')
                    ->where(fn ($query) => $query->where('user_id', Auth::user()->id)),
                new NotOwnPost(),
            ],
        ];
    }
}
