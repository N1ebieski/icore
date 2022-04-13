<?php

namespace N1ebieski\ICore\Models\Comment\Post;

use N1ebieski\ICore\Models\Comment\Comment as BaseComment;
use N1ebieski\ICore\Database\Factories\Comment\Post\CommentFactory;

class Comment extends BaseComment
{
    // Configuration

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Comment\Comment::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\Comment\Post\CommentFactory::new();
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'post';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\ICore\Models\Post::class;
    }

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CommentFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
