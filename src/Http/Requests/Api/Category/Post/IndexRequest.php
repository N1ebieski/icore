<?php

namespace N1ebieski\ICore\Http\Requests\Api\Category\Post;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\ValueObjects\Category\Status;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Requests\Api\Category\IndexRequest as BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    /**
     * Undocumented variable
     *
     * @var Category
     */
    protected $category;

    /**
     * Undocumented function
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
            'filter.parent' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists($this->category->getTable(), $this->category->getKeyName())
                    ->where(function ($query) {
                        $query->where('model_type', $this->category->model_type)
                            ->when(
                                !optional($this->user())->can('admin.categories.view'),
                                function ($query) {
                                    $query->where('status', Status::ACTIVE);
                                }
                            );
                    })
            ]
        ]);
    }
}
