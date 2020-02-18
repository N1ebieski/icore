<?php

namespace N1ebieski\ICore\Http\ViewComponents\Tag;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\View\View;

/**
 * [TagComponent description]
 */
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
     * [__construct description]
     * @param Tag  $tag [description]
     * @param int  $limit [description]
     * @param array $cats [description]
     */
    public function __construct(Tag $tag, int $limit = 25, array $colors = null)
    {
        $this->tag = $tag;

        $this->limit = $limit;
        $this->colors = $colors;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('icore::web.components.tag.tag', [
            'tags' => $this->tag->makeCache()->rememberPopularByComponent([
                'limit' => $this->limit,
            ]),
            'colors' => $this->colors
        ]);
    }
}
