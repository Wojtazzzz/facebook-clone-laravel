<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use App\Rules\Friend;
use App\Rules\NotSelfId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;

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
                'nullable',
                'required_without:images',
                'string',
                'max:200',
            ],

            'images' => [
                'nullable',
                'required_without:content',
                'array',
            ],

            'images.*' => [
                'required',
                File::image(),
            ],

            'receiver_id' => [
                'required',
                'integer',
                'exists:users,id',
                new NotSelfId(),
                new Friend(),
            ],
        ];
    }
}
