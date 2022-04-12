<?php

namespace N1ebieski\ICore\Models\Comment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Cache\CommentCache;
use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\CommentService;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Repositories\CommentRepo;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use N1ebieski\ICore\Models\Traits\HasRealDepth;
use Franzose\ClosureTable\Extensions\QueryBuilder;
use N1ebieski\ICore\Models\Comment\CommentClosure;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comment extends Entity
{
    use FullTextSearchable;
    use Filterable;
    use Polymorphic;
    use Carbonable;
    use HasRealDepth;
    use HasFactory;

    // Configuration

    /**
     * [public description]
     * @var int
     */
    public const ACTIVE = 1;

    /**
     * [public description]
     * @var int
     */
    public const INACTIVE = 0;

    /**
     * [public description]
     * @var int
     */
    public const CENSORED = 1;

    /**
     * [public description]
     * @var int
     */
    public const UNCENSORED = 0;

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
    protected $table = 'comments';

    /**
     * ClosureTable model instance.
     *
     * @var CommentClosure
     */
    protected $closure = 'N1ebieski\ICore\Models\Comment\CommentClosure';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content_html', 'status', 'censored'];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['content'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::INACTIVE,
        'censored' => self::UNCENSORED
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'model_id' => 'integer',
        'parent_id' => 'integer',
        'status' => 'integer',
        'censored' => 'integer',
        'position' => 'integer',
        'real_depth' => 'integer',
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
        return \N1ebieski\ICore\Database\Factories\Comment\CommentFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function ancestors(): BelongsToMany
    {
        return $this->belongsToMany(
            \N1ebieski\ICore\Models\Comment\Comment::class,
            'comments_closure',
            'descendant',
            'ancestor'
        );
    }

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function descendants(): BelongsToMany
    {
        return $this->belongsToMany(
            \N1ebieski\ICore\Models\Comment\Comment::class,
            'comments_closure',
            'ancestor',
            'descendant'
        );
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function childrens(): HasMany
    {
        return $this->hasMany(\N1ebieski\ICore\Models\Comment\Comment::class, 'parent_id');
    }

    // public function childrensRecursive()
    // {
    //     return $this->childrens()->with('childrensRecursive');
    // }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\Rating\Comment\Rating::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\Report\Comment\Report::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\User::class);
    }

    /**
     * Undocumented function
     *
     * @return MorphTo
     */
    public function morph(): MorphTo
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    // Accessors

    /**
     * [getTypeAttribute description]
     * @return string [description]
     */
    public function getTypeAttribute(): string
    {
         return \N1ebieski\ICore\Models\Comment\Comment::class;
    }

    /**
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute(): string
    {
         return strip_tags($this->attributes['content_html']);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getContentAsHtmlAttribute(): string
    {
        return nl2br(e($this->content_html, false));
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliSelfAttribute(): string
    {
        return 'comment';
    }

    // Mutators

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public function setContentAttribute(string $value): void
    {
        $this->attributes['content'] = strip_tags(preg_replace('/\s+/', ' ', str_replace(['\n', '\r'], '', $value)));
    }

    // Checkers

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return $this->status === static::ACTIVE;
    }

    /**
     * [isCommentable description]
     * @return bool [description]
     */
    public function isCommentable(): bool
    {
        return $this->isActive()
            && $this->getRelation('morph') !== null
            && $this->getRelation('morph')->isActive()
            && $this->getRelation('morph')->isCommentable();
    }

    // Scopes

    /**
     * [scopeFilterCommentsOrderBy description]
     * @param  Builder $query   [description]
     * @param  string|null  $orderby [description]
     * @return Builder          [description]
     */
    public function scopeFilterCommentsOrderBy(Builder $query, string $orderby = null): Builder
    {
        $order = explode('|', $orderby);

        if (count($order) == 2) {
            return $query->orderBy($order[0], $order[1])->orderBy('id', 'asc');
        }

        return $query->oldest();
    }

    /**
     * [scopeFilterCensored description]
     * @param  Builder $query    [description]
     * @param  [type]  $censored [description]
     * @return Builder|null           [description]
     */
    public function scopeFilterCensored(Builder $query, $censored = null)
    {
        $query->when($censored !== null, function ($query) use ($censored) {
            return $query->where('censored', $censored);
        });
    }

    /**
     * [scopeFilterExcept description]
     * @param  Builder $query  [description]
     * @param  array|null  $except [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterExcept(Builder $query, array $except = null)
    {
        $query->when($except !== null, function ($query) use ($except) {
            return $query->whereNotIn('comments.id', $except);
        });
    }

    /**
     * [scopeWithSumRating description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSumRating(Builder $query): Builder
    {
        return $query->withCount([
            'ratings AS sum_rating' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(`ratings`.`rating`), 0) as `sum_rating`'));
            }
        ]);
    }

    /**
     * [scopeWithAllRels description]
     * @param  Builder $query   [description]
     * @param  string|null  $orderby [description]
     * @return Builder          [description]
     */
    public function scopeWithAllRels(Builder $query, string $orderby = null): Builder
    {
        return $query->withSumRating()
            ->with([
                'user:id,name',
                'morph',
                'ratings',
                'childrens' => function ($query) use ($orderby) {
                    $query->withSumRating()
                        ->with([
                            'user:id,name',
                            'morph',
                            'ratings',
                            'childrens' => function ($query) use ($orderby) {
                                $query->withSumRating()
                                    ->with(['user:id,name', 'ratings', 'morph'])
                                    ->withCount([
                                        'childrens' => function ($query) {
                                            $query->active();
                                        }
                                    ])
                                    ->active()
                                    ->filterCommentsOrderBy($orderby);
                            }
                        ])
                        ->active()
                        ->filterCommentsOrderBy($orderby);
                }
            ]);
    }

    /**
     * [scopeUncensored description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeUncensored(Builder $query): Builder
    {
        return $query->where('censored', static::UNCENSORED);
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', static::ACTIVE);
    }

    /**
     * [scopeRoot description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * [scopeInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', static::INACTIVE);
    }

    // Loads

    /**
     * [loadAllRels description]
     * @return self [description]
     */
    public function loadAllRels(): self
    {
        return $this->loadCount('reports')->load('morph');
    }

    /**
     * [loadAncestorsAndChildrens description]
     * @return self [description]
     */
    public function loadAncestorsAndChildrens(): self
    {
        return $this->load([
            'ancestors' => function ($q) {
                $q->with('user:id,name')->whereColumn('ancestor', '!=', 'descendant')
                    ->orderBy('depth', 'desc');
            },
            'childrens' => function ($q) {
                $q->with('user:id,name')->orderBy('created_at', 'asc');
            }
        ]);
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

        $query->poli();

        return $query;
    }

    /**
     * Model jest polimotficzny. Trzeba dodać warunek sprawdzający model_type
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
            ->poli()
            ->orderBy($positionColumn, 'desc')
            ->first();

        return !is_null($entity) ? (int)$entity->position : null;
    }

    // Factories

    /**
     * [makeRepo description]
     * @return CommentRepo [description]
     */
    public function makeRepo()
    {
        return App::make(CommentRepo::class, ['comment' => $this]);
    }

    /**
     * [makeCache description]
     * @return CommentCache [description]
     */
    public function makeCache()
    {
        return App::make(CommentCache::class, ['comment' => $this]);
    }

    /**
     * [makeService description]
     * @return CommentService [description]
     */
    public function makeService()
    {
        return App::make(CommentService::class, ['comment' => $this]);
    }
}
