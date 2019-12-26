<?php

namespace N1ebieski\ICore\Models\Comment;

use Franzose\ClosureTable\Models\Entity;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use N1ebieski\ICore\Models\Comment\CommentClosure;
use Franzose\ClosureTable\Extensions\QueryBuilder;
use N1ebieski\ICore\Repositories\CommentRepo;
use N1ebieski\ICore\Cache\CommentCache;
use N1ebieski\ICore\Services\CommentService;

class Comment extends Entity implements CommentInterface
{
    use FullTextSearchable, Filterable, Polymorphic;

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
        'status' => 0,
        'censored' => false
    ];

    // Relations

    /**
     * [ancestors description]
     * @return [type] [description]
     */
    public function ancestors()
    {
        return $this->belongsToMany('N1ebieski\ICore\Models\Comment\Comment', 'comments_closure', 'descendant', 'ancestor');
    }

    /**
     * [descendants description]
     * @return [type] [description]
     */
    public function descendants()
    {
        return $this->belongsToMany('N1ebieski\ICore\Models\Comment\Comment', 'comments_closure', 'ancestor', 'descendant');
    }

    /**
     * [childrens description]
     * @return [type] [description]
     */
    public function childrens()
    {
        return $this->hasMany('N1ebieski\ICore\Models\Comment\Comment', 'parent_id');
    }

    // public function childrensRecursive()
    // {
    //     return $this->childrens()->with('childrensRecursive');
    // }

    /**
     * [ratings description]
     * @return [type] [description]
     */
    public function ratings()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Rating\Comment\Rating', 'model');
    }

    /**
     * [reports description]
     * @return [type] [description]
     */
    public function reports()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Report\Comment\Report', 'model');
    }

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [morph description]
     * @return [type] [description]
     */
    public function morph()
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    // Accessors

    /**
     * [getTypeAttribute description]
     * @return string [description]
     */
    public function getTypeAttribute() : string
    {
         return 'N1ebieski\ICore\\Models\\Comment\\Comment';
    }

    /**
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute() : string
    {
         return strip_tags($this->attributes['content_html']);
    }

    /**
     * [getCreatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getCreatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * [getUpdatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getUpdatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliSelfAttribute() : string
    {
        return 'comment';
    }

    // Mutators

    /**
     * [setContentAttribute description]
     * @param void $value [description]
     */
    public function setContentAttribute($value) : void
    {
        $this->attributes['content'] = strip_tags(preg_replace('/\s+/', ' ', str_replace(['\n', '\r'], '', $value)));
    }

    // Checkers

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive() : bool
    {
        return $this->status === 1;
    }

    /**
     * [isCommentable description]
     * @return bool [description]
     */
    public function isCommentable() : bool
    {
        return $this->isActive()
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
    public function scopeFilterCommentsOrderBy(Builder $query, string $orderby = null) : Builder
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
        if ($censored !== null) return $query->where('censored', $censored);
    }

    /**
     * [scopeFilterExcept description]
     * @param  Builder $query  [description]
     * @param  array|null  $except [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterExcept(Builder $query, array $except = null)
    {
        if ($except !== null) return $query->whereNotIn('comments.id', $except);
    }

    /**
     * [scopeWithSumRating description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSumRating(Builder $query) : Builder
    {
        return $query->selectRaw('comments.*, COALESCE(SUM(ratings.rating), 0) AS sum_rating')
            ->leftJoin('ratings', function($q) {
                 $q->on('ratings.model_id', '=', 'comments.id');
                 $q->where('ratings.model_type', '=', 'N1ebieski\ICore\Models\Comment\Comment');
            })
            ->groupBy('comments.id');
    }

    /**
     * [scopeWithAllRels description]
     * @param  Builder $query   [description]
     * @param  string|null  $orderby [description]
     * @return Builder          [description]
     */
    public function scopeWithAllRels(Builder $query, string $orderby = null) : Builder
    {
        return $query->withSumRating()
            ->with([
                'user:id,name',
                'ratings',
                'childrens' => function($query) use ($orderby) {
                    $query->withSumRating()
                    ->with(['user:id,name', 'ratings'])
                    ->with(['childrens' => function($query) use ($orderby) {
                        $query->withSumRating()
                        ->with(['user:id,name', 'ratings'])
                        ->withCount([
                            'childrens' => function($query) {
                                $query->active();
                            }
                        ])
                        ->active()
                        ->filterCommentsOrderBy($orderby);
                    }])
                    ->active()
                    ->filterCommentsOrderBy($orderby);
                }
            ]);
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->whereStatus(1);
    }

    /**
     * [scopeInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeInactive(Builder $query) : Builder
    {
        return $query->whereStatus(0);
    }

    // Loads

    /**
     * [loadAllRels description]
     * @return self [description]
     */
    public function loadAllRels() : self
    {
        // Laravel nie posiada loadCount dla modelu? Serio?
        // $this->reports_count = $this->reports()->count();
        $this->loadCount('reports');
        $this->load('morph:id,title,comment');

        return $this;
    }

    /**
     * [loadAncestorsAndChildrens description]
     * @return self [description]
     */
    public function loadAncestorsAndChildrens() : self
    {
        $this->load([
            'ancestors' => function($q) {
                $q->with('user:id,name')->whereColumn('ancestor', '!=', 'descendant')
                ->orderBy('depth', 'desc');
            },
            'childrens' => function($q) {
                $q->with('user:id,name')->orderBy('created_at', 'asc');
            }
        ]);

        return $this;
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

    // Makers

    /**
     * [makeRepo description]
     * @return CommentRepo [description]
     */
    public function makeRepo() : CommentRepo
    {
        return app()->make(CommentRepo::class, ['comment' => $this]);
    }

    /**
     * [makeCache description]
     * @return CommentCache [description]
     */
    public function makeCache() : CommentCache
    {
        return app()->make(CommentCache::class, ['comment' => $this]);
    }

    /**
     * [makeService description]
     * @return CommentService [description]
     */
    public function makeService() : CommentService
    {
        return app()->make(CommentService::class, ['comment' => $this]);
    }
}
