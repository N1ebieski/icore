<?php

namespace N1ebieski\ICore\Http\Requests\Api\User;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Rules\NotPresent;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;

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
            'page' => 'bail|integer',
            'filter' => 'bail|array',
            'filter.except' => 'bail|nullable|array',
            'filter.except.*' => 'bail|integer',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => optional($this->user())->can('admin.users.view') ?
                'bail|nullable|integer|in:0,1'
                : App::make(NotPresent::class),
            'filter.role' => [
                'bail',
                'nullable',
                'exists:roles,id'
            ],
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc'
                . optional($this->user())->can('admin.users.view') ?
                    ',updated_at|asc,updated_at|desc'
                    : null
            ],
            'filter.paginate' => [
                'bail',
                'integer',
                Rule::in([$paginate, ($paginate * 2), ($paginate * 4)]),
            ]
        ];
    }
}
