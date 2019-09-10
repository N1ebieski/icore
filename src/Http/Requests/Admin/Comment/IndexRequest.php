<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment;

use Illuminate\Foundation\Http\FormRequest;
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
        $paginate = config('icore.database.paginate');

        return [
            'page' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.search' => 'string|min:3|max:255',
            'filter.status' => 'integer|in:0,1|no_js_validation',
            'filter.author' => 'integer|exists:users,id|no_js_validation',
            'filter.censored' => 'integer|in:0,1|no_js_validation',
            'filter.report' => 'integer|in:0,1|no_js_validation',
            'filter.orderby' => [
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }
}
