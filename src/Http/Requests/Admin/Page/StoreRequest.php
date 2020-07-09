<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;

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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
            ]);
        }

        if ($this->has('tags')) {
            $this->merge([
                'tags' => explode(
                    ',',
                    Config::get('icore.tag.normalizer') !== null ?
                        Config::get('icore.tag.normalizer')($this->input('tags'))
                        : $this->input('tags')
                )
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
            'tags' => 'array|between:0,' . Config::get('icore.page.max_tags'),
            'tags.*' => 'min:3|max:30|alpha_num_spaces|distinct',
            'seo_title' => 'max:255',
            'seo_desc' => 'max:255',
            'icon' => 'nullable|string|max:255',
            'seo_noindex' => 'boolean',
            'seo_nofollow' => 'boolean',
            'comment' => 'boolean',
            'status' => 'required|in:0,1',
            'parent_id' => 'nullable|integer|exists:pages,id'
        ];
    }
}
