<?php

namespace N1ebieski\ICore\Models\Page;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Cache\PageCache;
use Cviebrock\EloquentTaggable\Taggable;
use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\PageService;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Repositories\PageRepo;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Page\PageInterface;
use Illuminate\Support\Collection as Collect;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;

/**
 * [Page description]
 */
class Page extends Entity implements PageInterface
{
    use Sluggable, Taggable, FullTextSearchable, Filterable, PivotEventTrait;

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
    public const WITH_COMMENT = 1;

    /**
     * [public description]
     * @var int
     */
    public const WITHOUT_COMMENT = 0;

    /**
     * [public description]
     * @var int
     */
    public const SEO_NOINDEX = 1;

    /**
     * [public description]
     * @var int
     */
    public const SEO_INDEX = 0;

    /**
     * [public description]
     * @var int
     */
    public const SEO_NOFOLLOW = 1;

    /**
     * [public description]
     * @var int
     */
    public const SEO_FOLLOW = 0;

    /**
     * [private description]
     * @var bool
     */
    private $pivotEvent = false;

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
    protected $table = 'pages';

    /**
     * ClosureTable model instance.
     *
     * @var PageClosure
     */
    protected $closure = 'N1ebieski\ICore\Models\Page\PageClosure';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content_html',
        'content',
        'seo_title',
        'seo_desc',
        'seo_noindex',
        'seo_nofollow',
        'status',
        'comment',
        'position',
        'icon'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'content'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'seo_noindex' => self::SEO_INDEX,
        'seo_nofollow' => self::SEO_FOLLOW,
        'status' => self::ACTIVE,
        'comment' => self::WITHOUT_COMMENT,
        'icon' => null
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'seo_noindex' => 'integer',
        'seo_nofollow' => 'integer',
        'status' => 'integer',
        'comment' => 'integer',
        'parent_id' => 'integer',
        'position' => 'integer',
        'real_depth' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    // Overrides

    public static function boot()
    {
        parent::boot();

        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if ($model->pivotEvent === false && in_array($relationName, ['tags'])) {
                $model->fireModelEvent('updated');
                $model->pivotEvent = true;
            }
        });

        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if ($model->pivotEvent === false && in_array($relationName, ['tags'])) {
                $model->fireModelEvent('updated');
                $model->pivotEvent = true;
            }
        });
    }

    /**
     * Override relacji tags, bo ma hardcodowane nazwy pól
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        $model = config('taggable.model');

        return $this->morphToMany($model, 'model', 'tags_models', 'model_id', 'tag_id')
            ->withTimestamps();
    }

    // Relations

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [comments description]
     * @return [type] [description]
     */
    public function comments()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Comment\Comment', 'model');
    }

    /**
     * [ancestors description]
     * @return [type] [description]
     */
    public function ancestors()
    {
        return $this->belongsToMany('N1ebieski\ICore\Models\Page\Page', 'pages_closure', 'descendant', 'ancestor');
    }

    /**
     * [descendants description]
     * @return [type] [description]
     */
    public function descendants()
    {
        return $this->belongsToMany('N1ebieski\ICore\Models\Page\Page', 'pages_closure', 'ancestor', 'descendant');
    }

    /**
     * [childrens description]
     * @return [type] [description]
     */
    public function childrens()
    {
        return $this->hasMany('N1ebieski\ICore\Models\Page\Page', 'parent_id');
    }

    /**
     * [childrensRecursiveWithAllRels description]
     * @return [type] [description]
     */
    public function childrensRecursiveWithAllRels()
    {
        return $this->childrens()->withRecursiveAllRels();
    }

    // Loads

    /**
     * [loadAncestorsExceptSelf description]
     * @return self [description]
     */
    public function loadAncestorsExceptSelf() : self
    {
        return $this->load(['ancestors' => function ($q) {
            $q->whereColumn('ancestor', '!=', 'descendant')->orderBy('depth', 'desc');
        }]);
    }

    /**
     * [loadRecursiveAllRels description]
     * @return self [description]
     */
    public function loadRecursiveChildrens() : self
    {
        return $this->load([
                'childrensRecursiveWithAllRels' => function ($query) {
                    $query->active()->orderBy('position', 'asc');
                },
            ]);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function loadActiveSiblings() : self
    {
        return $this->setRelation(
            'siblings',
            $this->where('parent_id', $this->parent_id)
                ->active()
                ->orderBy('position', 'asc')
                ->get()
        );
    }

    // Accessors

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute() : string
    {
        return get_class($this);
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliSelfAttribute() : string
    {
        return 'page';
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
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute() : string
    {
        return Purifier::clean($this->attributes['content_html']);
    }

    /**
     * [getMetaTitleAttribute description]
     * @return string [description]
     */
    public function getMetaTitleAttribute() : string
    {
        return (!empty($this->attributes['seo_title'])) ? $this->attributes['seo_title'] : $this->title;
    }

    /**
     * [getMetaDescAttribute description]
     * @return string [description]
     */
    public function getMetaDescAttribute() : string
    {
        return (!empty($this->attributes['seo_desc'])) ? $this->attributes['seo_desc'] : $this->shortContent;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getReplacementContentAttribute() : string
    {
        $replacement = Collect::make(Config::get('icore.replacement'));

        return str_replace(
            $replacement->keys()->toArray(),
            $replacement->values()->toArray(),
            $this->content
        );
    }

    /**
     * Short content used in the listing
     * @return string [description]
     */
    public function getShortContentAttribute() : string
    {
        return substr(strip_tags($this->replacement_content), 0, 500);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getReplacementContentHtmlAttribute() : string
    {
        $replacement = Collect::make(Config::get('icore.replacement'));

        return str_replace(
            $replacement->keys()->toArray(),
            $replacement->values()->toArray(),
            Purifier::clean($this->content_html)
        );
    }

    /**
     * Full content without more link
     * @return string [description]
     */
    public function getNoMoreContentHtmlAttribute() : string
    {
        return str_replace('[more]', '', $this->replacement_content_html);
    }

    /**
     * Content to the point of more link
     * @return string [description]
     */
    public function getLessContentHtmlAttribute() : string
    {
        $cut = explode('<p>[more]</p>', $this->replacement_content_html);

        return (!empty($cut[1])) ? $cut[0] . '<a href="' . URL::route('web.page.show', [
                'page' => $this->slug,
                '#more'
            ]) . '">' . Lang::get('icore::pages.more') . '</a>' : $this->replacement_content_html;
    }

    /**
     * [getRealPositionAttribute description]
     * @return string [description]
     */
    public function getRealPositionAttribute() : string
    {
        return $this->position + 1;
    }

    /**
     * [getShortNameAttribute description]
     * @return string [description]
     */
    public function getShortTitleAttribute() : string
    {
        return (strlen($this->title) > 15) ? substr($this->title, 0, 15) : $this->title;
    }

    /**
     * [getFirstImageAttribute description]
     * @return string|null [description]
     */
    public function getFirstImageAttribute() : ?string
    {
        preg_match('/<img.+src=[\'|"](.*?)[\'|"]/', $this->content_html, $image);

        return $image[1] ?? null;
    }

    // Mutators

    /**
     * [setContentAttribute description]
     * @param void $value [description]
     */
    public function setContentAttribute($value) : void
    {
        $this->attributes['content'] = strip_tags(str_replace('[more]', '', $value));
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isRedirect() : bool
    {
        return preg_match('/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/', $this->content);
    }

    /**
     * [isCommentable description]
     * @return bool [description]
     */
    public function isCommentable() : bool
    {
        return $this->comment === static::WITH_COMMENT;
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive() : bool
    {
        return $this->status === static::ACTIVE;
    }

    // Scopes

    /**
    * [scopeFilterParent description]
    * @param  Builder $query  [description]
    * @param  mixed  $parent [description]
    * @return Builder|null          [description]
    */
    public function scopeFilterParent(Builder $query, $parent = null) : ?Builder
    {
        return $query->when($parent !== null, function ($query) use ($parent) {
            $query->where('parent_id', $parent->id ?? null);
        });
    }

    /**
    * [scopeWithAncestorsExceptSelf description]
    * @param  Builder $query [description]
    * @return Builder        [description]
    */
    public function scopeWithAncestorsExceptSelf(Builder $query) : Builder
    {
        return $query->with(['ancestors' => function ($q) {
            $q->whereColumn('ancestor', '!=', 'descendant')->orderBy('depth', 'desc');
        }]);
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where('status', static::ACTIVE);
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

    /**
     * [scopeWithRecursiveAllRels description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithRecursiveAllRels(Builder $query) : Builder
    {
        return $query->with([
            'childrensRecursiveWithAllRels' => function ($query) {
                $query->active()->orderBy('position', 'asc');
            }
        ]);
    }

    /**
     * [scopeComponentOnly description]
     * @param  Builder $query [description]
     * @param  array|null  $only  [description]
     * @return Builder|null         [description]
     */
    public function scopeComponentOnly(Builder $query, array $only = null) : ?Builder
    {
        return $query->when($only !== null, function ($query) use ($only) {
            $query->whereIn('id', $only);
        });
    }

    // Makers

    /**
     * [makeRepo description]
     * @return PageRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PageRepo::class, ['page' => $this]);
    }

    /**
     * [makeCache description]
     * @return PageCache [description]
     */
    public function makeCache()
    {
        return App::make(PageCache::class, ['page' => $this]);
    }

    /**
     * [makeService description]
     * @return PageService [description]
     */
    public function makeService()
    {
        return App::make(PageService::class, ['page' => $this]);
    }
}
