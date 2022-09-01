<?php

namespace N1ebieski\ICore\View\Components\Comment;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Contracts\View\Factory as ViewFactory;

class LatestComponent implements Htmlable
{
    /**
     * [private description]
     * @var Comment
     */
    protected $comment;

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
     * Undocumented function
     *
     * @param Comment $comment
     * @param ViewFactory $view
     * @param integer $limit
     */
    public function __construct(Comment $comment, ViewFactory $view, int $limit = 5)
    {
        $this->comment = $comment;

        $this->view = $view;

        $this->limit = $limit;
    }

    /**
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.comment.latest', [
            'comments' => $this->comment->makeCache()->rememberLatestByComponent([
                'limit' => $this->limit,
            ])
        ])->render();
    }
}
