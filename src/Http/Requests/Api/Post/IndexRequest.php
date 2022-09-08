<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Requests\Api\Post;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Post\Status;

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
        $post = App::make(Post::class);

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
                    Status::ACTIVE,
                    optional($this->user())->can('admin.posts.view') ? Status::INACTIVE : null,
                    optional($this->user())->can('admin.posts.view') ? Status::SCHEDULED : null
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
                                !optional($this->user())->can('admin.posts.view'),
                                function ($query) {
                                    $query->where('status', Status::ACTIVE);
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
                    'Must be one of %1$s, %2$s (available only for admin.posts.view) or %3$s (available only for admin.posts.view)',
                    Status::ACTIVE,
                    Status::INACTIVE,
                    Status::SCHEDULED
                ),
                'example' => Status::ACTIVE
            ],
            'filter.category' => [
                'description' => 'ID of category contains posts.',
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
