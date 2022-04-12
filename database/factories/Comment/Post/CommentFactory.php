<?php

namespace N1ebieski\ICore\Database\Factories\Comment\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
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
    public function withPost()
    {
        return $this->for(
            Post::factory()->active()->commentable()->publish()->withUser(),
            'morph'
        );
    }
}
