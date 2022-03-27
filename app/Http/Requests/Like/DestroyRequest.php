<?php

namespace App\Http\Requests\Like;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DestroyRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::authorize('destroy', [Like::class, $this->route->post]);
    }

    public function rules()
    {
        return [
            //
        ];
    }
}
