<?php

declare(strict_types=1);

namespace App\Http\Requests\CommentLike;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class DestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $isExist = $this->comment
                ->likes()
                ->where('user_id', $this->user()->id)
                ->exists();

            if (! $isExist) {
                $validator->errors()->add('comment', 'Cannot unlike comment which is not liked');
            }
        });
    }
}
