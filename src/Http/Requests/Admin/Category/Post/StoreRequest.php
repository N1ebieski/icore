<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Category\Post;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreRequest as BaseStoreRequest;

class StoreRequest extends BaseStoreRequest
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
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')
                    ->where(function ($query) {
                        $query->where('model_type', $this->category->model_type);
                    })
            ]
        ]);
    }
}
