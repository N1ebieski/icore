<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\View\Components\Comment\Post;

use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\View\Components\Comment\CommentComponent as BaseCommentComponent;

class CommentComponent extends BaseCommentComponent
{
    /**
     *
     * @param Comment $comment
     * @param ViewFactory $view
     * @param int $limit
     * @param null|int $maxContent
     * @param string $orderby
     * @return void
     */
    public function __construct(
        Comment $comment,
        ViewFactory $view,
        int $limit = 5,
        ?int $maxContent = null,
        string $orderby = 'created_at|desc'
    ) {
        parent::__construct($comment, $view, $limit, $maxContent, $orderby);
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.comment.post.comment', [
            'comments' => $this->comment->makeCache()->rememberByComponent([
                'limit' => $this->limit,
                'max_content' => $this->maxContent,
                'orderby' => $this->orderby
            ])
        ]);
    }
}
