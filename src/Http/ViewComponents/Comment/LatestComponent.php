<?php

namespace N1ebieski\ICore\Http\ViewComponents\Comment;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\View\View;

/**
 * [LatestComponent description]
 */
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
     * @var int
     */
    protected $limit;

    /**
     * [__construct description]
     * @param Tag  $comment [description]
     * @param int  $limit [description]
     */
    public function __construct(Comment $comment, int $limit = 5)
    {
        $this->comment = $comment;

        $this->limit = $limit;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('icore::web.components.comment.latest', [
            'comments' => $this->comment->makeCache()->rememberLatestByComponent([
                'limit' => $this->limit,
            ])
        ]);
    }
}
