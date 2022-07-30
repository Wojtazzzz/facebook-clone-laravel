<?php

declare(strict_types=1);

namespace App\Http\Requests\Hidden\Post;

use App\Rules\NotHidden;
use App\Rules\NotOwnPost;
use App\Rules\NotSaved;
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
                new NotSaved(),
                new NotHidden(),
                new NotOwnPost(),
            ],
        ];
    }
}
