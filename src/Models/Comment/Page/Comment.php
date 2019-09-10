<?php

namespace N1ebieski\ICore\Models\Comment\Page;

use N1ebieski\ICore\Models\Comment\Comment as CommentBaseModel;
use N1ebieski\ICore\Models\Page\Page;

/**
 * [Comment description]
 */
class Comment extends CommentBaseModel
{
    /**
     * [protected description]
     * @var Page
     */
    protected $morph;

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

    // Setters

    /**
     * [setMorph description]
     * @param Page $page [description]
     * @return $this
     */
    public function setMorph(Page $page)
    {
        $this->morph = $page;

        return $this;
    }

    // Getters

    /**
     * [getMorph description]
     * @return Page [description]
     */
    public function getMorph()
    {
        return $this->morph;
    }
}
