<?php

namespace N1ebieski\ICore\View\Composers\Admin;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\View\Composers\Composer;
use N1ebieski\ICore\ValueObjects\Comment\Status;

class SidebarComposer extends Composer
{
    /**
     * Undocumented variable
     *
     * @var Collection
     */
    public $comments_inactive_count;

    /**
     * Undocumented variable
     *
     * @var Collection
     */
    public $comments_reported_count;

    /**
     * Undocumented function
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comments_inactive_count = $comment->makeRepo()->countByModelTypeAndStatus()
            ->where('status', Status::inactive());

        $this->comments_reported_count = $comment->makeRepo()->countReportedByModelType();
    }
}
