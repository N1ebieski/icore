<?php
namespace N1ebieski\ICore\Models\Category;

use Franzose\ClosureTable\Models\Entity;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Traits\FullTextSearchable;
use N1ebieski\ICore\Traits\Filterable;
use N1ebieski\ICore\Traits\Polymorphic;
use N1ebieski\ICore\Traits\Carbonable;
use Illuminate\Database\Eloquent\Builder;
use Franzose\ClosureTable\Extensions\QueryBuilder;
use N1ebieski\ICore\Repositories\CategoryRepo;
use N1ebieski\ICore\Cache\CategoryCache;
use N1ebieski\ICore\Services\CategoryService;

/**
 * [Category description]
 */
class Category extends Entity implements CategoryInterface
{
    use Sluggable, Filterable, FullTextSearchable, Polymorphic, Carbonable;

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
     * @var CategoryClosure
     */
    protected $closure = 'N1ebieski\ICore\Models\Category\CategoryClosure';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'icon', 'status'];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['name'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
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
        'status' => 1,
    ];

    // Relations

    /**
     * [ancestors description]
     * @return [type] [description]
     */
    public function ancestors()
    {
        return $this->belongsToMany('N1ebieski\ICore\Models\Category\Category', 'categories_closure', 'descendant', 'ancestor');
    }

    /**
     * [descendants description]
     * @return [type] [description]
     */
    public function descendants()
    {
        return $this->belongsToMany('N1ebieski\ICore\Models\Category\Category', 'categories_closure', 'ancestor', 'descendant');
    }

    /**
     * [childrens description]
     * @return [type] [description]
     */
    public function childrens()
    {
        return $this->hasMany('N1ebieski\ICore\Models\Category\Category', 'parent_id');
    }

    /**
     * Recursive relation of childrens()
     * @return [type] [description]
     */
    public function childrensRecursiveWithAllRels()
    {
        return $this->childrens()->withRecursiveAllRels();
    }

    /**
     * [posts description]
     * @return [type] [description]
     */
    public function posts()
    {
        return $this->morphedByMany('N1ebieski\ICore\Models\Post', 'model', 'categories_models', 'category_id');
    }

    // Overrides

    /**
     * Model jest polimorficzny i sprawdzanie rodzeństwa musi się odbywać z użyciem
     * $this->model_type
     *
     * Builds a part of the siblings query.
     *
     * @param string|int|array $direction
     * @param int|bool $parentId
     * @param string $order
     * @return QueryBuilder
     */
    protected function siblings($direction = '', $parentId = false, $order = 'asc')
    {
        $query = parent::siblings($direction, $parentId, $order);

        $query->poliType();

        return $query;
    }

    /**
     * Model jest polimorficzny. Trzeba dodać warunek sprawdzający model_type
     *
     * @param  bool $parentId [description]
     * @return [type]            [description]
     */
    public function getLastPosition($parentId = false)
    {
        $positionColumn = $this->getPositionColumn();
        $parentIdColumn = $this->getParentIdColumn();

        $parentId = ($parentId === false ? $this->parent_id : $parentId);

        $entity = $this->select($positionColumn)
            ->where($parentIdColumn, '=', $parentId)
            ->poliType()
            ->orderBy($positionColumn, 'desc')
            ->first();

        return !is_null($entity) ? (int)$entity->position : null;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        return $this->where('id', $value)->orWhere('slug', $value)
            ->WithAncestorsExceptSelf()->first() ?? abort(404);
    }

    // Scopes

    /**
     * [scopeWithAncestorsExceptSelf description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithAncestorsExceptSelf(Builder $query) : Builder
    {
        return $query->with(['ancestors' => function($q) {
            $q->whereColumn('ancestor', '!=', 'descendant')->orderBy('depth', 'desc');
        }]);
    }

    /**
     * [scopeWithRecursiveAllRels description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithRecursiveAllRels(Builder $query) : Builder
    {
        return $query->with([
            'childrensRecursiveWithAllRels' => function($query) {
                $query->withCount([
                    'posts' => function($query) {
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
            return $query->where('parent_id', $parent->id ?? null);
        }
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder         [description]
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->whereStatus(1);
    }

    /**
     * [scopeRoot description]
     * @param  Builder $query [description]
     * @return Builder         [description]
     */
    public function scopeRoot(Builder $query) : Builder
    {
        return $query->where('parent_id', null);
    }

    // Accessors

    /**
     * [getRealPositionAttribute description]
     * @return string [description]
     */
    public function getRealPositionAttribute() : string
    {
        return $this->position+1;
    }

    // Loads

    /**
     * [loadAncestorsExceptSelf description]
     * @return self [description]
     */
    public function loadAncestorsExceptSelf() : self
    {
        return $this->load(['ancestors' => function($q) {
            $q->whereColumn('ancestor', '!=', 'descendant')->orderBy('depth', 'desc');
        }]);;
    }

    // Getters

    /**
     * [getRepo description]
     * @return CategoryRepo [description]
     */
    public function getRepo() : CategoryRepo
    {
        return app()->make(CategoryRepo::class, ['category' => $this]);
    }

    /**
     * [getCache description]
     * @return CategoryCache [description]
     */
    public function getCache() : CategoryCache
    {
        return app()->make(CategoryCache::class, ['category' => $this]);
    }

    /**
     * [getService description]
     * @return CategoryService [description]
     */
    public function getService() : CategoryService
    {
        return app()->make(CategoryService::class, ['category' => $this]);
    }
}
