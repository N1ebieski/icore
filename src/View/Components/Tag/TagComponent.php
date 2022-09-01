<?php

namespace N1ebieski\ICore\View\Components\Tag;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;

class TagComponent implements Htmlable
{
    /**
     * [private description]
     * @var Tag
     */
    protected $tag;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $limit;

    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $colors;

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param ViewFactory $view
     * @param integer $limit
     * @param array $colors
     */
    public function __construct(Tag $tag, ViewFactory $view, int $limit = 25, array $colors = null)
    {
        $this->tag = $tag;

        $this->view = $view;

        $this->limit = $limit;
        $this->colors = $colors;
    }

    /**
     * 
     * @return string 
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.tag.tag', [
            'tags' => $this->tag->makeCache()->rememberPopularByComponent([
                'limit' => $this->limit,
            ]),
            'colors' => $this->colors
        ])->render();
    }
}
