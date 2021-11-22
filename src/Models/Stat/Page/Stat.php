<?php

namespace N1ebieski\ICore\Models\Stat\Page;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Models\Stat\Stat as BaseStat;

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
        return 'page';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return \N1ebieski\IPage\Models\Page\Page::class;
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\IPage\Models\Page\Page::class, 'model', 'stats_values');
    }
}
