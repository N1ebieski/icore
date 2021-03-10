<?php

namespace N1ebieski\ICore\Models\Comment\Page;

use N1ebieski\ICore\Models\Comment\Comment as CommentBaseModel;

/**
 * [Comment description]
 */
class Comment extends CommentBaseModel
{
    // Configuration

    /**
     * [getModelTypeAttribute description]
     * @return [type] [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\ICore\\Models\\Page\\Page';
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

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'page';
    }
}
