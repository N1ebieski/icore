<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Category;

use Illuminate\Validation\Rule;
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
            'name' => [
                'required',
                'string',
                'between:3,255',
                Rule::unique('categories', 'name')
                    ->where(function ($query) {
                        if ($this->input('parent_id') === null) {
                            $query->whereNull('parent_id');
                        } else {
                            $query->where('parent_id', $this->input('parent_id'));
                        }
                    })
            ],
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id'
        ];
    }
}
