<?php

namespace N1ebieski\ICore\Http\Requests\Admin\BanValue;

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
            'type' => 'required|string|in:ip,word',
            'page' => 'integer',
            'except' => 'filled|array',
            'except.*' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.search' => 'nullable|string|min:3|max:255',
            'filter.orderby' => [
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,value|asc,value|desc',
                'no_js_validation',
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed|null  $keys
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['type'] = $this->route('type');

        return $data;
    }
}
