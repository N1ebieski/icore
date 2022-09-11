<?php

namespace N1ebieski\ICore\View\Components\Tag\Post;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;

class TagComponent extends Component
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
        protected Tag $tag,
        protected ViewFactory $view,
        protected int $limit = 25,
        protected ?array $colors = null,
        protected ?array $cats = null
    ) {
        //
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
