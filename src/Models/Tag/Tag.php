<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Models\Tag;

use DateTime;
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
 * N1ebieski\ICore\Models\Tag\Tag
 *
 * @property int $tag_id
 * @property string $name
 * @property string $normalized
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property-read string $created_at_diff
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @method static Builder|Tag byName(string $value)
 * @method static \N1ebieski\ICore\Database\Factories\Tag\TagFactory factory(...$parameters)
 * @method static Builder|Tag filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Tag filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Tag filterExcept(?array $except = null)
 * @method static Builder|Tag filterOrderBy(?string $orderby = null)
 * @method static Builder|Tag filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Tag filterReport(?int $report = null)
 * @method static Builder|Tag filterSearch(?string $search = null)
 * @method static Builder|Tag filterStatus(?int $status = null)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag orderBySearch(string $term)
 * @method static Builder|Tag poli()
 * @method static Builder|Tag poliType()
 * @method static Builder|Tag query()
 * @method static Builder|Tag search(string $term)
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereNormalized($value)
 * @method static Builder|Tag whereTagId($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @method static Builder|Tag withCountSum()
 * @mixin \Eloquent
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
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeFilterOrderBySearch(Builder $query, string $search = null): Builder
    {
        return $query->when($search !== null, function ($query) {
            return $query->orderByRaw('LENGTH(name) ASC');
        });
    }

    /**
     * [scopeWithCountSum description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithCountSum(Builder $query): Builder
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
