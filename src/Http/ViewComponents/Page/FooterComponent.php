<?php

namespace N1ebieski\ICore\Http\ViewComponents\Page;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\View\View;

/**
 * [CategoryComponent description]
 */
class FooterComponent implements Htmlable
{
    /**
     * Model
     * @var Page
     */
    private $page;

    /**
     * [private description]
     * @var Collect
     */
    private $collect;

    /**
     * Pattern by cols and pages IDs example [[18, 19, 32], [45], [3, 1]]]
     * means 3 cols, first with pages 18 id, 19 id etc.
     * @var array|null
     */
    private $pattern;

    /**
     * Number of columns
     * @var int
     */
    private $cols;

    /**
     * [__construct description]
     * @param Page  $page [description]
     * @param Collect $collect [description]
     * @param array|null $pattern  [description]
     * @param int $cols [description]
     */
    public function __construct(Page $page, Collect $collect, array $pattern = null, int $cols = 3)
    {
        $this->page = $page;
        $this->collect = $collect;

        $this->pattern = $pattern;
        $this->cols = $cols;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        $pages = $this->page->getCache()->rememberWithRecursiveChildrensByComponent([
            'pattern' => $this->pattern !== null ?
                $this->collect->make($this->pattern)->collapse()->toArray()
                : null
        ]);

        return view('icore::web.components.page.footer.index', [
            'pages' => $pages,
            'cols' => $this->pattern ?: (int)round($pages->count() / $this->cols, 0),
            'pattern' => $this->pattern
        ]);
    }
}
