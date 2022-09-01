<?php

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
 * @property int $real_depth
 * @property Status $status
 * @property Category $category
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
