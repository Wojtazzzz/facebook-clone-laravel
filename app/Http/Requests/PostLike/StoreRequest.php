<?php

declare(strict_types=1);

namespace App\Http\Requests\PostLike;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class StoreRequest extends FormRequest
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
            $isExist = $this->post
                ->likes()
                ->where('user_id', $this->user()->id)
                ->exists();

            if ($isExist) {
                $validator->errors()->add('post', 'Cannot create second like for this post');
            }
        });
    }
}
