<?php

namespace N1ebieski\ICore\Models;

use Carbon\Carbon;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\Post\PostCache;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\ValueObjects\Post\Status;
use N1ebieski\ICore\Services\Post\PostService;
use N1ebieski\ICore\Repositories\Post\PostRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\ValueObjects\Post\SeoNoindex;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use N1ebieski\ICore\ValueObjects\Post\SeoNofollow;
use N1ebieski\ICore\Models\Traits\HasStatFilterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Database\Factories\Post\PostFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\ICore\ValueObjects\Post\Comment as Commentable;

/**
 * @property SeoNofollow $seo_nofollow
 * @property SeoNoindex $seo_noindex
 * @property Status $status
 * @property Comment $comment
 */
class Post extends Model
{
    use Sluggable;
    use Taggable;
    use HasFullTextSearchable;
    use PivotEventTrait;
    use HasCarbonable;
    use HasFactory;
    use HasFilterable, HasStatFilterable {
        HasStatFilterable::scopeFilterOrderBy insteadof HasFilterable;
    }

    // Configuration

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
        'comment' => Commentable::ACTIVE,
        'status' => Status::INACTIVE,
        'seo_noindex' => SeoNoindex::INACTIVE,
        'seo_nofollow' => SeoNofollow::INACTIVE
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
        'seo_noindex' => \N1ebieski\ICore\Casts\Post\SeoNoindexCast::class,
        'seo_nofollow' => \N1ebieski\ICore\Casts\Post\SeoNofollowCast::class,
        'status' => \N1ebieski\ICore\Casts\Post\StatusCast::class,
        'comment' => \N1ebieski\ICore\Casts\Post\CommentCast::class,
        'published_at' => 'datetime',
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
        return \N1ebieski\ICore\Database\Factories\Post\PostFactory::new();
    }

    // Overrides

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
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\ICore\Models\Category\Category::class,
            'model',
            'categories_models',
            'model_id',
            'category_id'
        );
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
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\Comment\Comment::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function stats(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\ICore\Models\Stat\Post\Stat::class,
            'model',
            'stats_values',
            'model_id',
            'stat_id'
        )->withPivot('value');
    }

    // Accessors

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute(): string
    {
        return get_class($this);
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliSelfAttribute(): string
    {
        return 'post';
    }

    /**
     * [getPublishedAtDiffAttribute description]
     * @return string [description]
     */
    public function getPublishedAtDiffAttribute(): string
    {
        return ($this->published_at != null) ?
            Carbon::parse($this->published_at)->diffForHumans(['parts' => 2])
            : '';
    }

    /**
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute(): string
    {
        return Purifier::clean($this->attributes['content_html']);
    }

    /**
     * [getMetaTitleAttribute description]
     * @return string [description]
     */
    public function getMetaTitleAttribute(): string
    {
        return (!empty($this->attributes['seo_title'])) ? $this->attributes['seo_title'] : $this->title;
    }

    /**
     * [getMetaDescAttribute description]
     * @return string [description]
     */
    public function getMetaDescAttribute(): string
    {
        return (!empty($this->attributes['seo_desc'])) ? $this->attributes['seo_desc'] : $this->shortContent;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getReplacementContentAttribute(): string
    {
        return App::make(\N1ebieski\ICore\Utils\Conversions\Replacement::class)
            ->handle($this->content, function ($value) {
                return $value;
            });
    }

    /**
     * Short content used in the listing
     * @return string [description]
     */
    public function getShortContentAttribute(): string
    {
        return mb_substr(
            e(strip_tags($this->replacement_content), false),
            0,
            Config::get('icore.post.short_content')
        );
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getReplacementContentHtmlAttribute(): string
    {
        return App::make(Pipeline::class)
            ->send($this->content_html)
            ->through([
                \N1ebieski\ICore\Utils\Conversions\Lightbox::class,
                \N1ebieski\ICore\Utils\Conversions\Replacement::class
            ])
            ->thenReturn();
    }

    /**
     * Full content without more link
     * @return string [description]
     */
    public function getNoMoreContentHtmlAttribute(): string
    {
        return str_replace(
            '<p>[more]</p>',
            '<span id="more" class="hashtag"></span>',
            $this->replacement_content_html
        );
    }

    /**
     * Content to the point of more link
     * @return string [description]
     */
    public function getLessContentHtmlAttribute(): string
    {
        $cut = explode('<p>[more]</p>', $this->replacement_content_html);

        return (!empty($cut[1])) ? $cut[0] . '<p><a href="' . URL::route('web.post.show', [
                $this->slug,
                '#more'
            ]) . '" class="more">' . Lang::get('icore::posts.more') . '</a></p>' : $this->replacement_content_html;
    }

    /**
     * [getFirstImageAttribute description]
     * @return string|null [description]
     */
    public function getFirstImageAttribute(): ?string
    {
        preg_match('/<img.+src=(?:\'|")(.*?)(?:\'|")/', $this->content_html, $image);

        return $image[1] ?? null;
    }

    // Mutators

    /**
     * [setPublishedAtAttribute description]
     * @param string|null $value [description]
     */
    public function setPublishedAtAttribute(string $value = null): void
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
    public function setContentAttribute(string $value): void
    {
        $this->attributes['content'] = !empty($value) ?
            strip_tags(str_replace('[more]', '', $value))
            : null;
    }

    // Scopes

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where([
            ["{$this->getTable()}.status", '=', Status::ACTIVE],
            ["{$this->getTable()}.published_at", '!=', null]
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where([
            ["{$this->getTable()}.status", '=', Status::SCHEDULED],
            ["{$this->getTable()}.published_at", '!=', null]
        ]);
    }

    // Loads

    /**
     *
     * @return static
     */
    public function loadAllRels()
    {
        return $this->load([
            'categories' => function ($query) {
                $query->withAncestorsExceptSelf();
            },
            'user',
            App::make(MigrationUtil::class)->contains('create_stats_table') ?
            'stats' : null
        ]);
    }

    // Factories

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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return PostFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
