<?php

namespace N1ebieski\ICore\Models\Tag;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Cache\Tag\TagCache;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\Tag\TagService;
use N1ebieski\ICore\Repositories\Tag\TagRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;
use Cviebrock\EloquentTaggable\Models\Tag as Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\Tag\TagFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;

/**
 * @property int $tag_id
 * 
 */
class Tag extends Taggable
{
    use HasFullTextSearchable;
    use HasPolymorphic;
    use HasFilterable;
    use HasCarbonable;
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\Tag\TagFactory::new();
    }

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @param string $search
     * @param string $orderby
     * @return Builder
     */
    public function scopeFilterOrderBySearch(Builder $query, string $search = null): Builder
    {
        return $query->when($search !== null, function ($query) use ($search) {
            return $query->orderByRaw('LENGTH(name) ASC');
        });
    }

    /**
     * [scopeWithSum description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSum(Builder $query): Builder
    {
        return $query->selectRaw('COUNT(`tags`.`tag_id`) AS `sum`')
            ->leftJoin('tags_models', 'tags.tag_id', '=', 'tags_models.tag_id')
            ->groupBy('tags.tag_id');
    }

    // Factories

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

    /**
     * [makeService description]
     * @return TagService [description]
     */
    public function makeService()
    {
        return App::make(TagService::class, ['tag' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return TagFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
