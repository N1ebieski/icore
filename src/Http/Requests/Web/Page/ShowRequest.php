<?php

namespace N1ebieski\ICore\Http\Requests\Web\Page;

use Illuminate\Foundation\Http\FormRequest;

class ShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !empty($this->page_cache->content_html);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'filled|integer',
            'except' => 'filled|array',
            'except.*' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,sum_rating|asc,sum_rating|desc'
            ]
        ];
    }
}
