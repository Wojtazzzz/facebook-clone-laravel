<?php

declare(strict_types=1);

namespace App\Http\Requests\Like;

use App\Rules\LikeNotExists;
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
            'post_id' => [
                'required',
                'integer',
                'exists:posts,id',
                new LikeNotExists(),
            ],
        ];
    }
}
