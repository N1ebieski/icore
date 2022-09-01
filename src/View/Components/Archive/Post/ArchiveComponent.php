<?php

namespace N1ebieski\ICore\View\Components\Archive\Post;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Post;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;

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

    /**
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.archive.post.archive', [
            'archives' => $this->post->makeCache()->rememberArchives()
        ])->render();
    }
}
