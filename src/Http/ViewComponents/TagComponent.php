<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Post;
use Illuminate\View\View;

/**
 * [TagComponent description]
 */
class TagComponent implements Htmlable
{
    /**
     * [private description]
     * @var Post
     */
    protected $post;

    /**
     * [__construct description]
     * @param Post $post [description]
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('icore::web.components.tag', [
            'tags' => $this->post->makeCache()->rememberPopularTags()
        ]);
    }
}
