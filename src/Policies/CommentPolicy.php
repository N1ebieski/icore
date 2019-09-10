<?php

namespace N1ebieski\ICore\Policies;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $current_user, Comment $comment)
    {
        return $current_user->id === $comment->user_id;
    }
}
