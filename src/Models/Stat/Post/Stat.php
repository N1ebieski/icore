<?php

namespace N1ebieski\ICore\Models\Stat\Post;

use N1ebieski\ICore\Models\Stat\Stat as BaseStat;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Stat extends BaseStat
{
    // Configuration

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Stat\Stat::class;
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
        return \N1ebieski\IPost\Models\Post::class;
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\IPost\Models\Post::class, 'model', 'stats_values');
    }
}
