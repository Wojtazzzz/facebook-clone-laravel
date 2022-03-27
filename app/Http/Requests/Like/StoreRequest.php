<?php

namespace App\Http\Requests\Like;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::authorize('create', [Like::class, Post::findOrFail($this->post_id)]);
    }

    public function rules()
    {
        return [
            'post_id' => [
                'required',
                'integer',
                'exists:posts,id'
            ]
        ];
    }
}
