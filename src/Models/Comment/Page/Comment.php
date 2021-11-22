<?php

namespace N1ebieski\ICore\Models\Comment\Page;

use N1ebieski\ICore\Models\Comment\Comment as BaseComment;

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

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\ICore\Models\Page\Page::class;
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'page';
    }
}
