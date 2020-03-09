<?php

namespace N1ebieski\ICore\View\Components\Category\Post;

use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\View;

/**
 * [CategoryComponent description]
 */
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
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.category.post.index', [
            'categories' => $this->category->makeCache()->rememberWithRecursiveChildrens()
        ]);
    }
}
