<?php

namespace N1ebieski\ICore\Models\Stat\Page;

use N1ebieski\ICore\Models\Stat\Stat as BaseStatModel;
use N1ebieski\ICore\Models\Page\Page;

class Stat extends BaseStatModel
{
    /**
     * [protected description]
     * @var Page
     */
    protected $morph;

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
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
        return $this->morphedByMany(\N1ebieski\IPage\Models\Page\Page::class, 'model', 'stats_values');
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
