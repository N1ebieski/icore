<?php

namespace N1ebieski\ICore\Http\Requests\Admin\BanModel\User;

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
            'filter' => 'array|no_js_validation',            
            'filter.search' => 'string|min:3|max:255',
            'filter.orderby' => [
                'in:bans_models.created_at|asc,bans_models.created_at|desc,bans_models.updated_at|asc,bans_models.updated_at|desc,users.name|asc,users.name|desc',
                'no_js_validation',
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }
}
