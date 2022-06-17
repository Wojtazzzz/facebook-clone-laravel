<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use App\Rules\Friend;
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
            'text' => [
                'required',
                'string',
                'max:200',
            ],

            'receiver_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn($this->user()->id),
                new Friend(),
            ],
        ];
    }
}
