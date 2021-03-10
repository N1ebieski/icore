<?php

namespace N1ebieski\ICore\Models\Stat\Post;

use N1ebieski\ICore\Models\Stat\Stat as BaseStatModel;

class Stat extends BaseStatModel
{
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
        return \N1ebieski\IPost\Models\Post::class;
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Stat\Stat::class;
    }

    // Relations

    /**
     * [morphs description]
     * @return [type] [description]
     */
    public function morphs()
    {
        return $this->morphedByMany(\N1ebieski\IPost\Models\Post::class, 'model', 'stats_values');
    }
}
