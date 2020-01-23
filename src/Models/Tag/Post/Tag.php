<?php

namespace N1ebieski\ICore\Models\Tag\Post;

use N1ebieski\ICore\Models\Tag\Tag as BaseTagModel;

class Tag extends BaseTagModel
{
    // Accessors

    /**
     * [getModelTypeAttribute description]
     * @return [type] [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\ICore\\Models\\Post';
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'post';
    }
}
