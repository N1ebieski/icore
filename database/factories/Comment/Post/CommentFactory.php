<?php

namespace N1ebieski\ICore\Database\Factories\Comment\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Database\Factories\Comment\CommentFactory as BaseCommentFactory;

class CommentFactory extends BaseCommentFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
    * Undocumented function
    *
    * @return static
    */
    public function withMorph()
    {
        return $this->for(
            Post::makeFactory()->active()->commentable()->publish()->withUser(),
            'morph'
        );
    }
}
