<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Link;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

/**
 * [CategoryComponent description]
 */
class LinkComponent implements Htmlable
{
    /**
     * [private description]
     * @var Link
     */
    protected $link;

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
     * Number of columns
     * @var int
     */
    protected $limit;

    /**
     * [protected description]
     * @var array|null
     */
    protected $cats;

    /**
     * Undocumented function
     *
     * @param Link $link
     * @param ViewFactory $view
     * @param Collect $collect
     * @param integer $limit
     * @param array $cats
     */
    public function __construct(
        Link $link,
        ViewFactory $view,
        Collect $collect,
        int $limit = 5,
        array $cats = null
    ) {
        $this->link = $link;

        $this->view = $view;
        $this->collect = $collect;

        $this->limit = $limit;
        $this->cats = $cats;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.link', [
            'links' => $this->link->makeCache()->rememberLinksByComponent([
                'cats' => $this->collect->make($this->cats)->flatten()->toArray(),
                'limit' => $this->limit
            ])
        ]);
    }
}
