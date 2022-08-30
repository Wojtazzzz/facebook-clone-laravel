<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use App\Rules\Friend;
use App\Rules\NotSelfId;
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
                'max:200',
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
