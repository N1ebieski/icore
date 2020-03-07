<?php

namespace N1ebieski\ICore\Policies;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Undocumented function
     *
     * @param User $current_user
     * @param Comment $comment
     * @return void
     */
    public function update(User $current_user, Comment $comment)
    {
        return $current_user->id === $comment->user_id;
    }
}
