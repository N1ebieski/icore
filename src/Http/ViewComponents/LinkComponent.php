<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Link;
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
     * [__construct description]
     * @param Link    $link    [description]
     * @param Collect $collect [description]
     * @param int     $limit [description]
     * @param array|null $cats [description]
     */
    public function __construct(Link $link, Collect $collect, int $limit = 5, array $cats = null)
    {
        $this->link = $link;
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
        $links = $this->link->makeCache()->rememberLinksByComponent([
            'cats' => $this->collect->make($this->cats)->flatten()->toArray(),
            'limit' => $this->limit
        ]);

        return view('icore::web.components.link', compact('links'));
    }
}
