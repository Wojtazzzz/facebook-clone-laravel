<?php

namespace App\Http\Requests\Like;

use App\Rules\LikeNotExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
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
