<?php

namespace N1ebieski\ICore\View\Components\Comment;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CommentComponent implements Htmlable
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
     * Undocumented variable
     *
     * @var int
     */
    protected $max_content;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $orderby;

    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param ViewFactory $view
     * @param integer $limit
     * @param integer $max_content
     * @param string $orderby
     */
    public function __construct(
        Comment $comment,
        ViewFactory $view,
        int $limit = 5,
        int $max_content = null,
        string $orderby = 'created_at|desc'
    ) {
        $this->comment = $comment;

        $this->view = $view;

        $this->limit = $limit;
        $this->max_content = $max_content;
        $this->orderby = $orderby;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml(): View
    {
        return $this->view->make('icore::web.components.comment.comment', [
            'comments' => $this->comment->makeCache()->rememberByComponent([
                'limit' => $this->limit,
                'max_content' => $this->max_content,
                'orderby' => $this->orderby
            ])
        ]);
    }
}
