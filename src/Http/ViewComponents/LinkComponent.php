<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
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
     * @param int     $limit [description]
     * @param array|null $cats [description]
     */
    public function __construct(Link $link, int $limit = 5, array $cats = null)
    {
        $this->link = $link;

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
            'cats' => $this->cats,
            'limit' => $this->limit
        ]);

        return view('icore::web.components.link', compact('links'));
    }
}
