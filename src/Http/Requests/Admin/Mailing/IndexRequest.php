<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Mailing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
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
        $paginate = Config::get('database.paginate');

        return [
            'page' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.except' => 'bail|filled|array',
            'filter.except.*' => 'bail|integer',            
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => 'bail|nullable|integer|in:0,1,2,3|no_js_validation',
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,title|asc,title|desc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }
}
