<?php

namespace N1ebieski\ICore\Models\Tag;

use Cviebrock\EloquentTaggable\Models\Tag as Taggable;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\ICore\Repositories\TagRepo;
use N1ebieski\ICore\Cache\TagCache;
use N1ebieski\ICore\Models\Traits\Polymorphic;

/**
 * [Tag description]
 */
class Tag extends Taggable
{
    use FullTextSearchable, Polymorphic;

    // Configuration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

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

    // Makers

    /**
     * [makeRepo description]
     * @return TagRepo [description]
     */
    public function makeRepo()
    {
        return App::make(TagRepo::class, ['tag' => $this]);
    }

    /**
     * [makeCache description]
     * @return TagCache [description]
     */
    public function makeCache()
    {
        return App::make(TagCache::class, ['tag' => $this]);
    }

    // /**
    //  * [makeService description]
    //  * @return TagService [description]
    //  */
    // public function makeService()
    // {
    //     return app()->make(TagService::class, ['tag' => $this]);
    // }
}
