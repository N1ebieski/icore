<?php

namespace N1ebieski\ICore\Http\ViewComponents;

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
     * [__construct description]
     * @param Category      $category      [description]
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('icore::web.components.category.index', [
            'categories' => $this->category->getCache()->rememberWithRecursiveChildrens()
        ]);
    }
}
