<?php

namespace N1ebieski\ICore\Http\Requests\Admin\BanValue;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as BaseRequest;

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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->route('type')) {
            $filter = [
                'filter' => $this->input('filter', []) + [
                    'type' => $this->route('type')
                ]
            ];

            App::make(BaseRequest::class)->merge($filter);

            $this->merge($filter);
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
            'filter' => 'array|no_js_validation',
            'filter.except' => 'bail|filled|array',
            'filter.except.*' => 'bail|integer',
            'filter.type' => 'required|string|in:ip,word',
            'filter.search' => 'nullable|string|min:3|max:255',
            'filter.orderby' => [
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,value|asc,value|desc',
                'no_js_validation',
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate * 2), ($paginate * 4)]) . '|integer|no_js_validation'
        ];
    }
}
