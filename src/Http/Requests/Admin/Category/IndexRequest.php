<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Category;

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
     * Undocumented function
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('filter.parent')) {
            $this->merge([
                'filter' => $this->input('filter') + [
                    '_parent' => $this->input('filter.parent') != 0 ?
                        $this->input('filter.parent')
                        : null
                    ]
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
        $paginate = Config::get('database.paginate');

        return [
            'page' => 'integer',
            'except' => 'filled|array',
            'except.*' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => 'bail|nullable|integer|in:0,1|no_js_validation',
            'filter._parent' => 'bail|nullable|integer|exists:categories,id|no_js_validation',
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,name|asc,name|desc,position|asc,position|desc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }
}
