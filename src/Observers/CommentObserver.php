<?php

namespace N1ebieski\ICore\Observers;

use Illuminate\Support\Facades\Cache;
use N1ebieski\ICore\Models\Comment\Comment;

class CommentObserver
{
    /**
     * Handle the link "saving" event.
     *
     * @param  Comment  $comment
     * @return void
     */
    public function saving(Comment $comment)
    {
        $comment->real_depth = $comment->getNextRealDepth();
    }

    /**
     * Handle the link "saved" event.
     *
     * @param  Comment  $comment
     * @return void
     */
    public function saved(Comment $comment)
    {
        $comment->reorderRealDepths();
    }

    /**
     * Handle the post "created" event.
     *
     * @param  Comment  $comment
     * @return void
     */
    public function created(Comment $comment)
    {
        Cache::tags(['comments', 'comment.' . $comment->poli . '.' . $comment->model_id])->flush();
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  Comment $comment
     * @return void
     */
    public function updated(Comment $comment)
    {
        Cache::tags(['comments', 'comment.' . $comment->poli . '.' . $comment->model_id])->flush();
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  Comment $comment
     * @return void
     */
    public function deleted(Comment $comment)
    {
        $comment->nextSiblings()->decrement($comment->getPositionColumn());

        Cache::tags(['comments', 'comment.' . $comment->poli . '.' . $comment->model_id])->flush();
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  Comment $comment
     * @return void
     */
    public function restored(Comment $comment)
    {
        //
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  Comment $comment
     * @return void
     */
    public function forceDeleted(Comment $comment)
    {
        //
    }
}
