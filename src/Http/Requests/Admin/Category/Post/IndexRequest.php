<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Category\Post;

use N1ebieski\ICore\Http\Requests\Admin\Category\IndexRequest as BaseIndexRequest;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Validation\Rule;

class IndexRequest extends BaseIndexRequest
{
    /**
     * [protected description]
     * @var Category
     */
    protected $category;

    /**
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        parent::__construct();

        $this->category = $category;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'filter._parent' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(function ($query) {
                        $query->where('model_type', $this->category->model_type);
                    })
            ]
        ]);
    }
}
