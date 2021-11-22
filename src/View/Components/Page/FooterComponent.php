<?php

namespace N1ebieski\ICore\View\Components\Page;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;

class FooterComponent implements Htmlable
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
     * @var Collect
     */
    protected $collect;

    /**
     * Pattern by cols and pages IDs example [[18, 19, 32], [45], [3, 1]]]
     * means 3 cols, first with pages 18 id, 19 id etc.
     * @var array|null
     */
    protected $pattern;

    /**
     * Number of columns
     * @var int
     */
    protected $cols;

    /**
     * Undocumented variable
     *
     * @var int|null
     */
    protected $maxDepth;

    /**
     * [__construct description]
     * @param Page  $page [description]
     * @param Collect $collect [description]
     * @param array|null $pattern  [description]
     * @param int $cols [description]
     */
    public function __construct(
        Page $page,
        Collect $collect,
        ViewFactory $view,
        array $pattern = null,
        int $cols = 3,
        int $maxDepth = null
    ) {
        $this->page = $page;

        $this->collect = $collect;
        $this->view = $view;

        $this->pattern = $pattern;
        $this->cols = $cols;
        $this->maxDepth = $maxDepth;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml(): View
    {
        $pages = $this->page->makeCache()->rememberWithRecursiveChildrensByComponent([
            'pattern' => $this->pattern !== null ?
                $this->collect->make($this->pattern)->collapse()->toArray()
                : null
        ]);

        return $this->view->make('icore::web.components.page.footer.index', [
            'pages' => $pages,
            'cols' => $this->pattern ?: (int)ceil($pages->count() / $this->cols),
            'pattern' => $this->pattern,
            'maxDepth' => $this->maxDepth
        ]);
    }
}
