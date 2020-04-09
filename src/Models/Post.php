<?php

namespace N1ebieski\ICore\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Cache\PostCache;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\PostService;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Repositories\PostRepo;
use N1ebieski\ICore\Models\Traits\Filterable;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * [Post description]
 */
class Post extends Model
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
    public const SCHEDULED = 2;

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
    private bool $pivotEvent = false;

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
        'published_at'
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'comment' => self::ACTIVE,
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
        'published_at' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        return $this->where('id', $value)
            ->with([
                'categories' => function ($query) {
                    $query->withAncestorsExceptSelf();
                },
            ])->first() ?? App::abort(404);
    }

    // Overrides

    public static function boot()
    {
        parent::boot();

        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if ($model->pivotEvent === false && in_array($relationName, ['categories', 'tags'])) {
                $model->fireModelEvent('updated');
                $model->pivotEvent = true;
            }
        });

        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if ($model->pivotEvent === false && in_array($relationName, ['categories', 'tags'])) {
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
     * [categories description]
     * @return [type] [description]
     */
    public function categories()
    {
        return $this->morphToMany(
            'N1ebieski\ICore\Models\Category\Category',
            'model',
            'categories_models',
            'model_id',
            'category_id'
        );
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
     * [comments description]
     * @return [type] [description]
     */
    public function comments()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Comment\Comment', 'model');
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
        return 'post';
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
     * [getPublishedAtDiffAttribute description]
     * @return string [description]
     */
    public function getPublishedAtDiffAttribute() : string
    {
        return ($this->published_at != null) ? Carbon::parse($this->published_at)->diffForHumans() : '';
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
     * Short content used in the listing
     * @return string [description]
     */
    public function getShortContentAttribute() : string
    {
        return mb_substr($this->content, 0, 500);
    }

    /**
     * Full content without more link
     * @return string [description]
     */
    public function getNoMoreContentHtmlAttribute() : string
    {
        return str_replace('[more]', '', $this->content_html);
    }

    /**
     * Content to the point of more link
     * @return string [description]
     */
    public function getLessContentHtmlAttribute() : string
    {
        $cut = explode('<p>[more]</p>', $this->content_html);

        return (!empty($cut[1])) ? $cut[0] . '<a href="' . URL::route('web.post.show', [
                'post' => $this->slug,
                '#more'
            ]) . '">' . Lang::get('icore::posts.more') . '</a>' : $this->content_html;
    }

    /**
     * [getFirstImageAttribute description]
     * @return string|null [description]
     */
    public function getFirstImageAttribute() : ?string
    {
        preg_match('/<img.+src=(?:\'|")(.*?)(?:\'|")/', $this->content_html, $image);

        return $image[1] ?? null;
    }

    // Mutators

    /**
     * [setPublishedAtAttribute description]
     * @param string|null $value [description]
     */
    public function setPublishedAtAttribute(string $value = null) : void
    {
        if ($value === null) {
            $this->attributes['published_at'] = null;
            return;
        }

        $this->attributes['published_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * [setContentAttribute description]
     * @param string $value [description]
     */
    public function setContentAttribute(string $value) : void
    {
        $this->attributes['content'] = strip_tags(str_replace('[more]', '', $value));
    }

    // Checkers

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
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where([
            ['posts.status', '=', static::ACTIVE],
            ['posts.published_at', '!=', null]
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduled(Builder $query) : Builder
    {
        return $query->where([
            ['posts.status', '=', static::SCHEDULED],
            ['posts.published_at', '!=', null]
        ]);
    }

    // Makers

    /**
     * [makeRepo description]
     * @return PostRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PostRepo::class, ['post' => $this]);
    }

    /**
     * [makeCache description]
     * @return PostCache [description]
     */
    public function makeCache()
    {
        return App::make(PostCache::class, ['post' => $this]);
    }

    /**
     * [makeService description]
     * @return PostService [description]
     */
    public function makeService()
    {
        return App::make(PostService::class, ['post' => $this]);
    }
}
