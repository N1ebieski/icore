<?php

namespace N1ebieski\ICore\Http\Requests\Api\Post;

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
                Rule::exists('categories', 'id')
                    ->where(function ($query) use ($post) {
                        $query->where('model_type', $post->getMorphClass())
                            ->when(
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
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,title|asc,title|desc,view|desc,view|asc'
            ],
            'filter.paginate' => [
                'bail',
                'integer',
                Rule::in([$paginate, ($paginate * 2), ($paginate * 4)])
            ]
        ];
    }
}