<?php

namespace N1ebieski\ICore\Models\Comment\Post;

use N1ebieski\ICore\Models\Comment\Comment as CommentBaseModel;
use N1ebieski\ICore\Models\Post;

/**
 * [Comment description]
 */
class Comment extends CommentBaseModel
{
    /**
     * [protected description]
     * @var Post
     */
    protected $morph;

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'post';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\ICore\\Models\\Post';
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\ICore\\Models\\Comment\\Comment';
    }

    // Setters

    /**
     * [setMorph description]
     * @param Post $post [description]
     * @return $this
     */
    public function setMorph(Post $post)
    {
        $this->morph = $post;

        return $this;
    }

    // Getters

    /**
     * [getMorph description]
     * @return Post [description]
     */
    public function getMorph()
    {
        return $this->morph;
    }
}
