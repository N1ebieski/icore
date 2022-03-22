<?php

namespace N1ebieski\ICore\Http\Requests\Api\User;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\User;
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
            'filter.status' => [
                'bail',
                'nullable',
                'integer',
                Rule::in([
                    User::ACTIVE,
                    optional($this->user())->can('admin.users.view') ? User::INACTIVE : null
                ])
            ],
            'filter.role' => [
                'bail',
                'nullable',
                'integer',
                'exists:roles,id'
            ],
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc'
            ],
            'filter.paginate' => [
                'bail',
                'nullable',
                'integer',
                Rule::in([$paginate, ($paginate * 2), ($paginate * 4)])
            ]
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'page' => [
                'example' => 1
            ],
            'filter.except.*' => [
                'description' => 'Array containing IDs, excluding records from the list.',
                'example' => []
            ],
            'filter.search' => [
                'description' => 'Search by keyword.',
                'example' => ''
            ],
            'filter.status' => [
                'description' => sprintf(
                    'Must be one of %1$s or %2$s (available only for admin.users.view)',
                    User::ACTIVE,
                    User::INACTIVE
                ),
                'example' => User::ACTIVE
            ],
            'filter.role' => [
                'example' => '',
            ],
            'filter.orderby' => [
                'description' => 'Sorting the result list.',
            ],
            'filter.paginate' => [
                'description' => 'Number of records in the list.',
                'example' => ''
            ]
        ];
    }
}
