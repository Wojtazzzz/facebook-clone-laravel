<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MarkAsReadRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'notifications' => [
                'required',
                'array'
            ],

            'notifications.*' => [
                'required',
                'string',
                'exists:notifications,id'
            ]
        ];
    }
}
