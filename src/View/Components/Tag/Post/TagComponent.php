<?php

namespace N1ebieski\ICore\View\Components\Tag\Post;

use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\View\Components\Tag\TagComponent as BaseTagComponent;

class TagComponent extends BaseTagComponent
{
    /**
     *
     * @param Tag $tag
     * @param ViewFactory $view
     * @param int $limit
     * @param null|array $colors
     * @param null|array $cats
     * @return void
     */
    public function __construct(
        Tag $tag,
        ViewFactory $view,
        int $limit = 25,
        ?array $colors = null,
        protected ?array $cats = null
    ) {
        parent::__construct($tag, $view, $limit, $colors);
    }

    /**
     *
     * @return View
     */
    public function render(): View
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
