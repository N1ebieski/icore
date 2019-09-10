<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCensoredRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'censored' => 'required|integer|in:0,1'
        ];
    }
}
