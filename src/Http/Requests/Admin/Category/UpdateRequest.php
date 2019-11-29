<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * [UpdateRequest description]
 */
class UpdateRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
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
            'name' => 'required|string|min:3|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function($query) {
                    $query->where('model_type', $this->category->model_type);
                }),
                Rule::notIn($this->category->makeRepo()->getDescendantsAsArray()),
            ]
        ];
    }
}
