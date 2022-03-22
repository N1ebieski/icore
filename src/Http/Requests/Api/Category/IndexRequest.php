<?php

namespace N1ebieski\ICore\Http\Requests\Api\Category;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Category\Category;

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
                'filter' => [
                    'real_parent' => $this->input('filter.parent'),
                    'parent' => !empty($this->input('filter.parent')) ?
                        $this->input('filter.parent')
                        : null
                ] + $this->input('filter')
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->has('filter.parent')) {
            $this->merge([
                'filter' => [
                    'parent' => $this->input('filter.real_parent')
                ] + $this->input('filter')
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
            'page' => 'bail|integer',
            'filter' => 'bail|array',
            'filter.except' => 'bail|nullable|array',
            'filter.except.*' => 'bail|integer',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => [
                'bail',
                'nullable',
                'integer',
                'in:1' . (
                    optional($this->user())->can('admin.categories.view') ? ',0' : null
                )
            ],
            'filter.parent' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->when(
                        !optional($this->user())->can('admin.categories.view'),
                        function ($query) {
                            $query->where('status', Category::ACTIVE);
                        }
                    );
                })
            ],
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,name|asc,name|desc,position|asc,position|desc,real_depth|asc,real_depth|desc'
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
                    'Must be one of %1$s or %2$s (available only for admin.categories.view)',
                    Category::ACTIVE,
                    Category::INACTIVE
                ),
                'example' => Category::ACTIVE
            ],
            'filter.parent' => [
                'description' => 'ID of category parent. If 0, shows only roots.',
                'example' => ''
            ],
            'filter.orderby' => [
                'description' => 'Sorting the result list.',
                'example' => ''
            ],
            'filter.paginate' => [
                'description' => 'Number of records in the list.',
                'example' => ''
            ]
        ];
    }
}
