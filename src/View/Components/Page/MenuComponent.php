<?php

namespace N1ebieski\ICore\View\Components\Page;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;

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
     * Pattern by pages IDs example [18, 19, 32]
     * @var array|null
     */
    protected $pattern;

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
    public function __construct(
        Page $page,
        ViewFactory $view,
        int $limit = 5,
        array $pattern = null
    ) {
        $this->page = $page;

        $this->view = $view;

        $this->limit = $limit;
        $this->pattern = $pattern;
    }

    /**
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.page.menu', [
            'pages' => $this->page->makeCache()->rememberWithChildrensByComponent([
                'limit' => $this->limit,
                'pattern' => $this->pattern
            ])
        ])->render();
    }
}
