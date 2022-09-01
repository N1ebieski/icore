<?php

namespace N1ebieski\ICore\View\Components\Category\Post;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CategoryComponent implements Htmlable
{
    /**
     * Model
     * @var Category
     */
    protected $category;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param ViewFactory $view
     */
    public function __construct(Category $category, ViewFactory $view)
    {
        $this->category = $category;

        $this->view = $view;
    }

    /**
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.category.post.index', [
            'categories' => $this->category->makeCache()->rememberWithRecursiveChildrens()
        ])->render();
    }
}
