<?php

namespace N1ebieski\ICore\Http\ViewComponents\Archive\Post;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Post;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

class ArchiveComponent implements Htmlable
{
    /**
     * Undocumented variable
     *
     * @var Post
     */
    protected $post;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented function
     *
     * @param Post $post
     * @param ViewFactory $view
     */
    public function __construct(Post $post, ViewFactory $view)
    {
        $this->post = $post;

        $this->view = $view;
    }

    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.archive.post.archive', [
            'archives' => $this->post->makeCache()->rememberArchives()
        ]);
    }
}
