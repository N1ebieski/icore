<?php

namespace N1ebieski\ICore\View\Components\Tag\Post;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\View\Components\Tag\TagComponent as BaseTagComponent;

/**
 * [TagComponent description]
 */
class TagComponent extends BaseTagComponent
{
    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $cats;

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param integer $limit
     * @param array $cats
     * @param array $colors
     */
    public function __construct(
        Tag $tag,
        ViewFactory $view,
        int $limit = 25,
        array $colors = null,
        array $cats = null
    ) {
        parent::__construct($tag, $view, $limit, $colors);

        $this->cats = $cats;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.tag.post.tag', [
            'tags' => $this->tag->makeCache()->rememberPopularByComponent([
                'limit' => $this->limit,
                'cats' => $this->cats
            ]),
            'colors' => $this->colors
        ]);
    }
}
