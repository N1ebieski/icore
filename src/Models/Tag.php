<?php

namespace N1ebieski\ICore\Models;

use Cviebrock\EloquentTaggable\Models\Tag as Taggable;
use N1ebieski\ICore\Traits\FullTextSearchable;
use N1ebieski\ICore\Services\TagService;
use N1ebieski\ICore\Repositories\TagRepo;
use N1ebieski\ICore\Cache\TagCache;

/**
 * [Tag description]
 */
class Tag extends Taggable
{
    use FullTextSearchable;

    // Configuration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'tags';

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['name'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'normalized';
    }

    // Getters

    /**
     * [getRepo description]
     * @return TagRepo [description]
     */
    public function getRepo() : TagRepo
    {
        return app()->make(TagRepo::class, ['tag' => $this]);
    }

    /**
     * [getCache description]
     * @return TagCache [description]
     */
    public function getCache() : TagCache
    {
        return app()->make(TagCache::class, ['tag' => $this]);
    }

    /**
     * [getService description]
     * @return TagService [description]
     */
    public function getService() : TagService
    {
        return app()->make(TagService::class, ['tag' => $this]);
    }
}
