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

namespace N1ebieski\ICore\Models\Category;

use Illuminate\Support\Facades\App;
use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\Cache\Category\CategoryCache;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;
use N1ebieski\ICore\ValueObjects\Category\Status;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Services\Category\CategoryService;
use N1ebieski\ICore\Repositories\Category\CategoryRepo;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use N1ebieski\ICore\Database\Factories\Category\CategoryFactory;
use N1ebieski\ICore\Models\Traits\HasFixForRealDepthClosureTable;
use N1ebieski\ICore\Models\Traits\HasFixForPolymorphicClosureTable;

/**
 * N1ebieski\ICore\Models\Category\Category
 *
 * @property int $real_depth
 * @property Status $status
 * @property int $siblings_count
 * @property int $id
 * @property string $model_type
 * @property string $slug
 * @property string|null $icon
 * @property string $name
 * @property int $parent_id
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $ancestors
 * @property-read int|null $ancestors_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $children
 * @property-read int|null $children_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $childrens
 * @property-read int|null $childrens_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $childrensRecursiveWithAllRels
 * @property-read int|null $childrens_recursive_with_all_rels_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $descendants
 * @property-read int|null $descendants_count
 * @property-read string $created_at_diff
 * @property-read string $poli
 * @property-read int $real_position
 * @property-read string $updated_at_diff
 * @property-read Category|null $parent
 * @method static Builder|Category active()
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] all($columns = ['*'])
 * @method static Builder|Entity ancestors()
 * @method static Builder|Entity ancestorsOf($id)
 * @method static Builder|Entity ancestorsWithSelf()
 * @method static Builder|Entity ancestorsWithSelfOf($id)
 * @method static Builder|Entity childAt($position)
 * @method static Builder|Entity childNode()
 * @method static Builder|Entity childNodeOf($id)
 * @method static Builder|Entity childOf($id, $position)
 * @method static Builder|Entity childrenRange($from, $to = null)
 * @method static Builder|Entity childrenRangeOf($id, $from, $to = null)
 * @method static Builder|Entity descendants()
 * @method static Builder|Entity descendantsOf($id)
 * @method static Builder|Entity descendantsWithSelf()
 * @method static Builder|Entity descendantsWithSelfOf($id)
 * @method static \N1ebieski\ICore\Database\Factories\Category\CategoryFactory factory(...$parameters)
 * @method static Builder|Category filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Category filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Category filterExcept(?array $except = null)
 * @method static Builder|Category filterOrderBy(?string $orderby = null)
 * @method static Builder|Category filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Category filterParent($parent = null)
 * @method static Builder|Category filterReport(?int $report = null)
 * @method static Builder|Category filterSearch(?string $search = null)
 * @method static Builder|Category filterStatus(?int $status = null)
 * @method static Builder|Category findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Entity firstChild()
 * @method static Builder|Entity firstChildOf($id)
 * @method static Builder|Entity firstSibling()
 * @method static Builder|Entity firstSiblingOf($id)
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] get($columns = ['*'])
 * @method static Builder|Entity lastChild()
 * @method static Builder|Entity lastChildOf($id)
 * @method static Builder|Entity lastSibling()
 * @method static Builder|Entity lastSiblingOf($id)
 * @method static Builder|Entity neighbors()
 * @method static Builder|Entity neighborsOf($id)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Entity nextSibling()
 * @method static Builder|Entity nextSiblingOf($id)
 * @method static Builder|Entity nextSiblings()
 * @method static Builder|Entity nextSiblingsOf($id)
 * @method static Builder|Category orderBySearch(string $term)
 * @method static Builder|Category poli()
 * @method static Builder|Category poliType()
 * @method static Builder|Entity prevSibling()
 * @method static Builder|Entity prevSiblingOf($id)
 * @method static Builder|Entity prevSiblings()
 * @method static Builder|Entity prevSiblingsOf($id)
 * @method static Builder|Category query()
 * @method static Builder|Category root()
 * @method static Builder|Category search(string $term)
 * @method static Builder|Category sibling()
 * @method static Builder|Entity siblingAt($position)
 * @method static Builder|Entity siblingOf($id)
 * @method static Builder|Entity siblingOfAt($id, $position)
 * @method static Builder|Entity siblings()
 * @method static Builder|Entity siblingsOf($id)
 * @method static Builder|Entity siblingsRange($from, $to = null)
 * @method static Builder|Entity siblingsRangeOf($id, $from, $to = null)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereIcon($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereModelType($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereParentId($value)
 * @method static Builder|Category wherePosition($value)
 * @method static Builder|Category whereRealDepth($value)
 * @method static Builder|Category whereSlug($value)
 * @method static Builder|Category whereStatus($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category withAncestorsExceptSelf()
 * @method static Builder|Category withRecursiveAllRels()
 * @method static Builder|Category withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class Category extends Entity
{
    use Sluggable;
    use HasFilterable;
    use HasFullTextSearchable;
    use HasPolymorphic;
    use HasCarbonable;
    use HasFactory;
    use HasFixForRealDepthClosureTable;
    use HasFixForPolymorphicClosureTable;

    // Configuration

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * ClosureTable model instance.
     *
     * @var string
     * @phpstan-ignore-next-line
     */
    protected $closure = 'N1ebieski\ICore\Models\Category\CategoryClosure';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'icon', 'status'];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => \N1ebieski\ICore\Casts\Category\StatusCast::class,
        'parent_id' => 'integer',
        'position' => 'integer',
        'real_depth' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => Status::ACTIVE,
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \N1ebieski\ICore\Database\Factories\Category\CategoryFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\Category\CategoryFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function ancestors(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'categories_closure', 'descendant', 'ancestor');
    }

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function descendants(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'categories_closure', 'ancestor', 'descendant');
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function childrens(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function childrensRecursiveWithAllRels(): HasMany
    {
        /**
         * @phpstan-ignore-next-line
         */
        return $this->childrens()->withRecursiveAllRels();
    }

    // Scopes

    /**
     * [scopeWithAncestorsExceptSelf description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithAncestorsExceptSelf(Builder $query): Builder
    {
        return $query->with(['ancestors' => function ($q) {
            $q->whereColumn('ancestor', '!=', 'descendant')->orderBy('depth', 'desc');
        }]);
    }

    /**
     * [scopeWithRecursiveAllRels description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithRecursiveAllRels(Builder $query): Builder
    {
        return $query->with([
            'childrensRecursiveWithAllRels' => function ($query) {
                $query->withCount([
                    'morphs' => function ($query) {
                        $query->active();
                    }
                ])
                ->active()
                ->orderBy('position', 'asc');
            }
        ]);
    }

    /**
     * [scopeFilterParent description]
     * @param  Builder $query  [description]
     * @param  mixed  $parent [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterParent(Builder $query, $parent = null)
    {
        if ($parent !== null) {
            return $query->where("{$this->getTable()}.parent_id", $parent->id ?? null);
        }
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder         [description]
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where("{$this->getTable()}.status", Status::ACTIVE);
    }

    /**
     * [scopeRoot description]
     * @param  Builder $query [description]
     * @return Builder         [description]
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->where("{$this->getTable()}.parent_id", null);
    }

    // Accessors

    /**
     * [getRealPositionAttribute description]
     * @return int [description]
     */
    public function getRealPositionAttribute(): int
    {
        return $this->position + 1;
    }

    // Loads

    /**
     * [loadAncestorsExceptSelf description]
     * @return self [description]
     */
    public function loadAncestorsExceptSelf(): self
    {
        return $this->load(['ancestors' => function ($q) {
            $q->whereColumn('ancestor', '!=', 'descendant')->orderBy('depth', 'desc');
        }]);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return CategoryRepo [description]
     */
    public function makeRepo()
    {
        return App::make(CategoryRepo::class, ['category' => $this]);
    }

    /**
     * [makeCache description]
     * @return CategoryCache [description]
     */
    public function makeCache()
    {
        return App::make(CategoryCache::class, ['category' => $this]);
    }

    /**
     * [makeService description]
     * @return CategoryService [description]
     */
    public function makeService()
    {
        return App::make(CategoryService::class, ['category' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CategoryFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
