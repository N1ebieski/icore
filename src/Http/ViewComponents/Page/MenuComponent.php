<?php

namespace N1ebieski\ICore\Http\ViewComponents\Page;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

/**
 * [CategoryComponent description]
 */
class MenuComponent implements Htmlable
{
    /**
     * Model
     * @var Page
     */
    protected $page;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * [private description]
     * @var string
     */
    protected $limit;

    /**
     * Undocumented function
     *
     * @param Page $page
     * @param ViewFactory $view
     * @param integer $limit
     */
    public function __construct(Page $page, ViewFactory $view, int $limit = 5)
    {
        $this->page = $page;

        $this->view = $view;

        $this->limit = $limit;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.page.menu', [
            'pages' => $this->page->makeCache()->rememberWithChildrensByComponent([
                'limit' => $this->limit
            ])
        ]);
    }
}
