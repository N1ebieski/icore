<?php

namespace N1ebieski\ICore\Http\Requests\Api\Post;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Category\Post\Category;

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

        /**
         * @var Post
         */
        $post = Post::make();

        return [
            'page' => [
                'integer'
            ],
            'filter' => [
                'bail',
                'array'
            ],
            'filter.except' => [
                'bail',
                'nullable',
                'array'
            ],
            'filter.except.*' => [
                'bail',
                'integer'
            ],
            'filter.search' => [
                'bail',
                'nullable',
                'string',
                'min:3',
                'max:255'
            ],
            'filter.status' => [
                'bail',
                'nullable',
                'integer',
                Rule::in([
                    Post::ACTIVE,
                    optional($this->user())->can('admin.posts.view') ? Post::INACTIVE : null,
                    optional($this->user())->can('admin.posts.view') ? Post::SCHEDULED : null
                ])
            ],
            'filter.category' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('posts', 'id')
                    ->where(function ($query) use ($post) {
                        $query->where('model_type', $post->getMorphClass())
                            ->when(
                                !optional($this->user())->can('admin.posts.view'),
                                function ($query) {
                                    $query->where('status', Category::ACTIVE);
                                }
                            );
                    })
            ],
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,title|asc,title|desc,view|desc,view|asc'
            ],
            'filter.paginate' => [
                'bail',
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
        $paginate = Config::get('database.paginate');

        return [
            'page' => [
                'example' => 1
            ],
            'filter.except' => [
                'description' => 'Array containing IDs, excluding records from the list.',
            ],
            'filter.search' => [
                'description' => 'Search by keyword.',
                'example' => ''
            ],
            'filter.status' => [
                'description' => sprintf(
                    'Must be one of %1$s, %2$s (available only for admin.posts.view) or %3$s (available only for admin.posts.view)',
                    Post::ACTIVE,
                    Post::INACTIVE,
                    Post::SCHEDULED
                ),
                'example' => Post::ACTIVE
            ],
            'filter.category' => [
                'description' => 'ID of category contains posts.',
                'example' => null
            ],
            'filter.orderby' => [
                'description' => 'Sorting the result list.',
            ],
            'filter.paginate' => [
                'description' => 'Number of records in the list.',
                'example' => Arr::random([$paginate, ($paginate * 2), ($paginate * 4)])
            ]
        ];
    }
}
