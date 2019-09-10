<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Category;

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
        $paginate = config('icore.database.paginate');

        return [
            'page' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.search' => 'string|min:3|max:255',
            'filter.status' => 'integer|in:0,1|no_js_validation',
            'filter._parent' => 'nullable|integer|exists:categories,id|no_js_validation',
            'filter.orderby' => [
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,name|asc,name|desc,position|asc,position|desc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }
}
