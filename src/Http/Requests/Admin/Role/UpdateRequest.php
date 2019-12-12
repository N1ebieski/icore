<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->role->isEditNotDefault();
    }

    protected function prepareForValidation()
    {
        if ($this->role->name === 'user') {
            $this->merge([
                'name' => 'user'
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|string|between:3,255|unique:roles,name,' . $this->role->id,
            'perm' => 'array|no_js_validation',
            'perm.*' => [
                'bail',
                'nullable',
                'string',
                'distinct',
                'exists:permissions,name',
                $this->role->name === 'user' ? Rule::in([
                    'create comments',
                    'suggest comments'
                ]) : null,
                'no_js_validation'
            ]
        ];
    }
}
