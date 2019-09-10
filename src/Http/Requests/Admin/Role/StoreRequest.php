<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'bail|required|string|between:3,255|unique:roles,name',
            'perm' => 'array|no_js_validation',
            'perm.*' => [
                'bail',
                'string',
                'distinct',
                'exists:permissions,name',
                'no_js_validation'
            ]
        ];
    }
}
