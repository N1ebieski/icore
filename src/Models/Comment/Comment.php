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

namespace N1ebieski\ICore\Models\Comment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\Comment\CommentCache;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;
use N1ebieski\ICore\ValueObjects\Comment\Censored;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use N1ebieski\ICore\Services\Comment\CommentService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use N1ebieski\ICore\Repositories\Comment\CommentRepo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use N1ebieski\ICore\Database\Factories\Comment\CommentFactory;
use N1ebieski\ICore\Models\Traits\HasFixForRealDepthClosureTable;
use N1ebieski\ICore\Models\Traits\HasFixForPolymorphicClosureTable;

/**
 * N1ebieski\ICore\Models\Comment\Comment
 *
 * @property int $real_depth
 * @property string $poli
 * @property Status $status
 * @property Censored $censored
 * @property Entity $morph
 * @property int $id
 * @property int|null $user_id
 * @property int $model_id
 * @property string $model_type
 * @property int $parent_id
 * @property string $content_html
 * @property string $content
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Comment[] $ancestors
 * @property-read int|null $ancestors_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Comment[] $children
 * @property-read int|null $children_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Comment[] $childrens
 * @property-read int|null $childrens_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Comment[] $descendants
 * @property-read int|null $descendants_count
 * @property-read string $content_as_html
 * @property-read string $created_at_diff
 * @property-read string $poli_self
 * @property-read string $type
 * @property-read string $updated_at_diff
 * @property-read Comment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Rating\Comment\Rating[] $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Report\Comment\Report[] $reports
 * @property-read int|null $reports_count
 * @property-read \N1ebieski\ICore\Models\User|null $user
 * @method static Builder|Comment active()
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
 * @method static \N1ebieski\ICore\Database\Factories\Comment\CommentFactory factory(...$parameters)
 * @method static Builder|Comment filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Comment filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Comment filterCensored(?int $censored = null)
 * @method static Builder|Comment filterCommentsOrderBy(?string $orderby = null)
 * @method static Builder|Comment filterExcept(?array $except = null)
 * @method static Builder|Comment filterOrderBy(?string $orderby = null)
 * @method static Builder|Comment filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Comment filterReport(?int $report = null)
 * @method static Builder|Comment filterSearch(?string $search = null)
 * @method static Builder|Comment filterStatus(?int $status = null)
 * @method static Builder|Entity firstChild()
 * @method static Builder|Entity firstChildOf($id)
 * @method static Builder|Entity firstSibling()
 * @method static Builder|Entity firstSiblingOf($id)
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] get($columns = ['*'])
 * @method static Builder|Comment inactive()
 * @method static Builder|Entity lastChild()
 * @method static Builder|Entity lastChildOf($id)
 * @method static Builder|Entity lastSibling()
 * @method static Builder|Entity lastSiblingOf($id)
 * @method static Builder|Entity neighbors()
 * @method static Builder|Entity neighborsOf($id)
 * @method static Builder|Comment newModelQuery()
 * @method static Builder|Comment newQuery()
 * @method static Builder|Entity nextSibling()
 * @method static Builder|Entity nextSiblingOf($id)
 * @method static Builder|Entity nextSiblings()
 * @method static Builder|Entity nextSiblingsOf($id)
 * @method static Builder|Comment orderBySearch(string $term)
 * @method static Builder|Comment poli()
 * @method static Builder|Comment poliType()
 * @method static Builder|Entity prevSibling()
 * @method static Builder|Entity prevSiblingOf($id)
 * @method static Builder|Entity prevSiblings()
 * @method static Builder|Entity prevSiblingsOf($id)
 * @method static Builder|Comment query()
 * @method static Builder|Comment root()
 * @method static Builder|Comment search(string $term)
 * @method static Builder|Comment sibling()
 * @method static Builder|Entity siblingAt($position)
 * @method static Builder|Entity siblingOf($id)
 * @method static Builder|Entity siblingOfAt($id, $position)
 * @method static Builder|Entity siblings()
 * @method static Builder|Entity siblingsOf($id)
 * @method static Builder|Entity siblingsRange($from, $to = null)
 * @method static Builder|Entity siblingsRangeOf($id, $from, $to = null)
 * @method static Builder|Comment uncensored()
 * @method static Builder|Comment whereCensored($value)
 * @method static Builder|Comment whereContent($value)
 * @method static Builder|Comment whereContentHtml($value)
 * @method static Builder|Comment whereCreatedAt($value)
 * @method static Builder|Comment whereId($value)
 * @method static Builder|Comment whereModelId($value)
 * @method static Builder|Comment whereModelType($value)
 * @method static Builder|Comment whereParentId($value)
 * @method static Builder|Comment wherePosition($value)
 * @method static Builder|Comment whereRealDepth($value)
 * @method static Builder|Comment whereStatus($value)
 * @method static Builder|Comment whereUpdatedAt($value)
 * @method static Builder|Comment whereUserId($value)
 * @method static Builder|Comment withAllRels(?string $orderby = null)
 * @method static Builder|Comment withSumRating()
 * @mixin \Eloquent
 */
class Comment extends Entity
{
    use HasFullTextSearchable;
    use HasFilterable;
    use HasPolymorphic;
    use HasCarbonable;
    use HasFixForRealDepthClosureTable;
    use HasFixForPolymorphicClosureTable;
    use HasFactory;

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
    protected $table = 'comments';

    /**
     * ClosureTable model instance.
     *
     * @var string
     * @phpstan-ignore-next-line
     */
    protected $closure = 'N1ebieski\ICore\Models\Comment\CommentClosure';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
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
        'status' => Status::INACTIVE,
        'censored' => Censored::INACTIVE
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
        'status' => \N1ebieski\ICore\Casts\Comment\StatusCast::class,
        'censored' => \N1ebieski\ICore\Casts\Comment\CensoredCast::class,
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
     * [isCommentable description]
     * @return bool [description]
     */
    public function isCommentable(): bool
    {
        return $this->status->isActive()
            && $this->getRelation('morph') !== null
            && $this->getRelation('morph')->status->isActive()
            && $this->getRelation('morph')->comment->isActive();
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
     * @param  int|null  $censored [description]
     * @return Builder|null           [description]
     */
    public function scopeFilterCensored(Builder $query, int $censored = null)
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
        /**
         * @phpstan-ignore-next-line
         */
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
        return $query->where('censored', Censored::INACTIVE);
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::ACTIVE);
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
        return $query->where('status', Status::INACTIVE);
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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CommentFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
