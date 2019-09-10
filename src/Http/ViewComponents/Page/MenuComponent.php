<?php

namespace N1ebieski\ICore\Http\ViewComponents\Page;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Cache\PageCache;
use Illuminate\View\View;

/**
 * [CategoryComponent description]
 */
class MenuComponent implements Htmlable
{
    /**
     * Model
     * @var PageCache
     */
    private $pageCache;

    /**
     * [private description]
     * @var array
     */
    private $config;

    /**
     * [__construct description]
     * @param PageCache    $pageCache  [description]
     * @param int $limit [description]
     */
    public function __construct(PageCache $pageCache, int $limit = 5)
    {
        $this->pageCache = $pageCache;
        $this->config['limit'] = $limit;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('icore::web.components.page.menu', [
            'pages' => $this->pageCache->rememberWithChildrensByComponent($this->config)
        ]);
    }
}
