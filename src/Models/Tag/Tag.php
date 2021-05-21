<?php

namespace N1ebieski\ICore\Models\Tag;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Cache\TagCache;
use N1ebieski\ICore\Repositories\TagRepo;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Cviebrock\EloquentTaggable\Models\Tag as Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

class Tag extends Taggable
{
    use FullTextSearchable, Polymorphic, Filterable, Carbonable;

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
    public $searchable = ['name'];

    // /**
    //  * Get the route key for the model.
    //  *
    //  * @return string
    //  */
    // public function getRouteKeyName()
    // {
    //     return 'normalized';
    // }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tag_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes

    /**
     * [scopeWithSum description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSum(Builder $query) : Builder
    {
        return $query->selectRaw('COUNT(`tags`.`tag_id`) AS `sum`')
            ->leftJoin('tags_models', 'tags.tag_id', '=', 'tags_models.tag_id')
            ->groupBy('tags.tag_id');
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
