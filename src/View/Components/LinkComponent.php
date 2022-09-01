<?php

namespace N1ebieski\ICore\View\Components;

use Illuminate\View\View;
use Illuminate\Http\Request;
use N1ebieski\ICore\Models\Link;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;

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
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

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
     * @param Request $request
     * @param integer $limit
     * @param array $cats
     */
    public function __construct(
        Link $link,
        ViewFactory $view,
        Collect $collect,
        Request $request,
        int $limit = 5,
        array $cats = null
    ) {
        $this->link = $link;

        $this->view = $view;
        $this->collect = $collect;
        $this->request = $request;

        $this->limit = $limit;
        $this->cats = $cats;
    }

    /**
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.link', [
            'links' => $this->link->makeCache()->rememberLinksByComponent([
                'home' => $this->request->is('/'),
                'cats' => $this->collect->make($this->cats)->flatten()->toArray(),
                'limit' => $this->limit
            ])
        ])->render();
    }
}
