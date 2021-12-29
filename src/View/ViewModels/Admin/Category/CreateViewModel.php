<?php

namespace N1ebieski\ICore\View\ViewModels\Admin\Category;

use Illuminate\Http\Request;
use Spatie\ViewModels\ViewModel;
use N1ebieski\ICore\Models\Category\Category;

class CreateViewModel extends ViewModel
{
    /**
     * Undocumented variable
     *
     * @var Category
     */
    public $category;

    /**
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Request $request
     */
    public function __construct(Category $category, Request $request)
    {
        $this->category = $category;

        $this->request = $request;
    }

    /**
     * Undocumented function
     *
     * @return Category|null
     */
    public function parent(): ?Category
    {
        if ($this->request->input('parent_id') !== null) {
            return $this->category->find($this->request->input('parent_id'))
                ->loadAncestorsExceptSelf();
        }

        return null;
    }
}
