<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFullRequest extends FormRequest
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
            'title' => 'required|min:3|max:255',
            'seo_title' => 'max:255',
            'seo_desc' => 'max:255',
            'icon' => 'nullable|string|max:255',
            'seo_noindex' => 'boolean',
            'seo_nofollow' => 'boolean',
            'comment' => 'boolean',
            'status' => 'required|in:0,1',
            'parent_id' => [
                'nullable',
                'integer',
                'exists:pages,id',
                Rule::notIn($this->page->getRepo()->getDescendantsAsArray()),
            ]
        ];
    }
}
