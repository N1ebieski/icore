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
 * N1ebieski\ICore\Models\Post
 *
 * @property string $title
 * @property SeoNofollow $seo_nofollow
 * @property SeoNoindex $seo_noindex
 * @property Status $status
 * @property Commentable $comment
 * @property int $id
 * @property string $slug
 * @property int $user_id
 * @property string $content_html
 * @property string|null $content
 * @property string|null $seo_title
 * @property string|null $seo_desc
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\N1ebieski\ICore\Models\Category\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\N1ebieski\ICore\Models\Comment\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read string $created_at_diff
 * @property-read string|null $first_image
 * @property-read string $less_content_html
 * @property-read string $meta_desc
 * @property-read string $meta_title
 * @property-read string $model_type
 * @property-read string $no_more_content_html
 * @property-read string $poli_self
 * @property-read string $published_at_diff
 * @property-read string $replacement_content
 * @property-read string $replacement_content_html
 * @property-read string $short_content
 * @property-read array $tag_array
 * @property-read array $tag_array_normalized
 * @property-read string $tag_list
 * @property-read string $tag_list_normalized
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Stat\Post\Stat[] $stats
 * @property-read int|null $stats_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Tag\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \N1ebieski\ICore\Models\User $user
 * @method static Builder|Post active()
 * @method static \N1ebieski\ICore\Database\Factories\Post\PostFactory factory(...$parameters)
 * @method static Builder|Post filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Post filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Post filterExcept(?array $except = null)
 * @method static Builder|Post filterOrderBy(?string $orderby = null)
 * @method static Builder|Post filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Post filterReport(?int $report = null)
 * @method static Builder|Post filterSearch(?string $search = null)
 * @method static Builder|Post filterStatus(?int $status = null)
 * @method static Builder|Post findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Post isNotTagged()
 * @method static Builder|Post isTagged()
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post orderBySearch(string $term)
 * @method static Builder|Post query()
 * @method static Builder|Post scheduled()
 * @method static Builder|Post search(string $term)
 * @method static Builder|Post whereComment($value)
 * @method static Builder|Post whereContent($value)
 * @method static Builder|Post whereContentHtml($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post wherePublishedAt($value)
 * @method static Builder|Post whereSeoDesc($value)
 * @method static Builder|Post whereSeoNofollow($value)
 * @method static Builder|Post whereSeoNoindex($value)
 * @method static Builder|Post whereSeoTitle($value)
 * @method static Builder|Post whereSlug($value)
 * @method static Builder|Post whereStatus($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post whereUserId($value)
 * @method static Builder|Post withAllTags($tags)
 * @method static Builder|Post withAnyTags($tags)
 * @method static Builder|Post withCountStats(string $stat)
 * @method static Builder|Post withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static Builder|Post withoutAllTags($tags, bool $includeUntagged = false)
 * @method static Builder|Post withoutAnyTags($tags, bool $includeUntagged = false)
 * @mixin \Eloquent
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
     * @var array<string>
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
        return $this::class;
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
        return Purifier::clean($this->attributes['content_html'] ?? '');
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
            ->handle($this->content ?? '', function ($value) {
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
            ->send($this->content_html ?? '')
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

        /**
         * @var string
         */
        $lang = Lang::get('icore::posts.more');

        return (!empty($cut[1])) ? $cut[0] . '<p><a href="' . URL::route('web.post.show', [
                $this->slug,
                '#more'
            ]) . '" class="more">' . $lang . '</a></p>' : $this->replacement_content_html;
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
