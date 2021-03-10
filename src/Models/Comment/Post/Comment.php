<?php

namespace N1ebieski\ICore\Models\Comment\Post;

use N1ebieski\ICore\Models\Comment\Comment as CommentBaseModel;

/**
 * [Comment description]
 */
class Comment extends CommentBaseModel
{
    // Configuration

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
}
