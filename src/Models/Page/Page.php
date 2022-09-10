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

namespace N1ebieski\ICore\Models\Page;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Lang;
use Cviebrock\EloquentTaggable\Taggable;
use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\Page\PageCache;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\ValueObjects\Page\Status;
use Franzose\ClosureTable\Models\ClosureTable;
use N1ebieski\ICore\Services\Page\PageService;
use N1ebieski\ICore\Repositories\Page\PageRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\ValueObjects\Page\SeoNoindex;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use N1ebieski\ICore\ValueObjects\Page\SeoNofollow;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Models\Traits\HasStatFilterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Database\Factories\Page\PageFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use N1ebieski\ICore\ValueObjects\Page\Comment as Commentable;
use N1ebieski\ICore\Models\Traits\HasFixForRealDepthClosureTable;

/**
 * N1ebieski\ICore\Models\Page\Page
 *
 * @property int $real_depth
 * @property SeoNofollow $seo_nofollow
 * @property SeoNoindex $seo_noindex
 * @property Status $status
 * @property Commentable $comment
 * @property int $siblings_count
 * @property int $id
 * @property string $slug
 * @property int $user_id
 * @property string|null $icon
 * @property string $title
 * @property string $content_html
 * @property string|null $content
 * @property string|null $seo_title
 * @property string|null $seo_desc
 * @property int $parent_id
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Page[] $ancestors
 * @property-read int|null $ancestors_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Page[] $children
 * @property-read int|null $children_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Page[] $childrens
 * @property-read int|null $childrens_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Page[] $childrensRecursiveWithAllRels
 * @property-read int|null $childrens_recursive_with_all_rels_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\N1ebieski\ICore\Models\Comment\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Page[] $descendants
 * @property-read int|null $descendants_count
 * @property-read string $created_at_diff
 * @property-read string|null $first_image
 * @property-read string $less_content_html
 * @property-read string $meta_desc
 * @property-read string $meta_title
 * @property-read string $model_type
 * @property-read string $no_more_content_html
 * @property-read string $poli_self
 * @property-read int $real_position
 * @property-read string $replacement_content
 * @property-read string $replacement_content_html
 * @property-read string $short_content
 * @property-read string $short_title
 * @property-read array $tag_array
 * @property-read array $tag_array_normalized
 * @property-read string $tag_list
 * @property-read string $tag_list_normalized
 * @property-read string $updated_at_diff
 * @property-read Page|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Stat\Page\Stat[] $stats
 * @property-read int|null $stats_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Tag\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \N1ebieski\ICore\Models\User|null $user
 * @method static Builder|Page active()
 * @method static Builder|Page activeByDate()
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
 * @method static Builder|Page componentOnly(?array $only = null)
 * @method static Builder|Entity descendants()
 * @method static Builder|Entity descendantsOf($id)
 * @method static Builder|Entity descendantsWithSelf()
 * @method static Builder|Entity descendantsWithSelfOf($id)
 * @method static \N1ebieski\ICore\Database\Factories\Page\PageFactory factory(...$parameters)
 * @method static Builder|Page filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Page filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Page filterExcept(?array $except = null)
 * @method static Builder|Page filterOrderBy(?string $orderby = null)
 * @method static Builder|Page filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Page filterParent($parent = null)
 * @method static Builder|Page filterReport(?int $report = null)
 * @method static Builder|Page filterSearch(?string $search = null)
 * @method static Builder|Page filterStatus(?int $status = null)
 * @method static Builder|Page findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Entity firstChild()
 * @method static Builder|Entity firstChildOf($id)
 * @method static Builder|Entity firstSibling()
 * @method static Builder|Entity firstSiblingOf($id)
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] get($columns = ['*'])
 * @method static Builder|Page isNotTagged()
 * @method static Builder|Page isTagged()
 * @method static Builder|Entity lastChild()
 * @method static Builder|Entity lastChildOf($id)
 * @method static Builder|Entity lastSibling()
 * @method static Builder|Entity lastSiblingOf($id)
 * @method static Builder|Entity neighbors()
 * @method static Builder|Entity neighborsOf($id)
 * @method static Builder|Page newModelQuery()
 * @method static Builder|Page newQuery()
 * @method static Builder|Entity nextSibling()
 * @method static Builder|Entity nextSiblingOf($id)
 * @method static Builder|Entity nextSiblings()
 * @method static Builder|Entity nextSiblingsOf($id)
 * @method static Builder|Page orderBySearch(string $term)
 * @method static Builder|Entity prevSibling()
 * @method static Builder|Entity prevSiblingOf($id)
 * @method static Builder|Entity prevSiblings()
 * @method static Builder|Entity prevSiblingsOf($id)
 * @method static Builder|Page query()
 * @method static Builder|Page root()
 * @method static Builder|Page search(string $term)
 * @method static Builder|Entity sibling()
 * @method static Builder|Entity siblingAt($position)
 * @method static Builder|Entity siblingOf($id)
 * @method static Builder|Entity siblingOfAt($id, $position)
 * @method static Builder|Entity siblings()
 * @method static Builder|Entity siblingsOf($id)
 * @method static Builder|Entity siblingsRange($from, $to = null)
 * @method static Builder|Entity siblingsRangeOf($id, $from, $to = null)
 * @method static Builder|Page whereComment($value)
 * @method static Builder|Page whereContent($value)
 * @method static Builder|Page whereContentHtml($value)
 * @method static Builder|Page whereCreatedAt($value)
 * @method static Builder|Page whereIcon($value)
 * @method static Builder|Page whereId($value)
 * @method static Builder|Page whereParentId($value)
 * @method static Builder|Page wherePosition($value)
 * @method static Builder|Page whereRealDepth($value)
 * @method static Builder|Page whereSeoDesc($value)
 * @method static Builder|Page whereSeoNofollow($value)
 * @method static Builder|Page whereSeoNoindex($value)
 * @method static Builder|Page whereSeoTitle($value)
 * @method static Builder|Page whereSlug($value)
 * @method static Builder|Page whereStatus($value)
 * @method static Builder|Page whereTitle($value)
 * @method static Builder|Page whereUpdatedAt($value)
 * @method static Builder|Page whereUserId($value)
 * @method static Builder|Page withAllTags($tags)
 * @method static Builder|Page withAncestorsExceptSelf()
 * @method static Builder|Page withAnyTags($tags)
 * @method static Builder|Page withCountStats(string $stat)
 * @method static Builder|Page withRecursiveAllRels()
 * @method static Builder|Page withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static Builder|Page withoutAllTags($tags, bool $includeUntagged = false)
 * @method static Builder|Page withoutAnyTags($tags, bool $includeUntagged = false)
 * @mixin \Eloquent
 */
class Page extends Entity
{
    use Sluggable;
    use Taggable;
    use HasFullTextSearchable;
    use PivotEventTrait;
    use HasCarbonable;
    use HasFactory;
    use HasFixForRealDepthClosureTable;
    use HasFilterable, HasStatFilterable {
        HasStatFilterable::scopeFilterOrderBy insteadof HasFilterable;
    }

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
    protected $table = 'pages';

    /**
     * ClosureTable model instance.
     *
     * @var ClosureTable
     */
    // @phpstan-ignore-next-line
    protected $closure = 'N1ebieski\ICore\Models\Page\PageClosure';

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
    public function sluggable(): array
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
        'seo_noindex' => SeoNoindex::INACTIVE,
        'seo_nofollow' => SeoNofollow::INACTIVE,
        'status' => Status::INACTIVE,
        'comment' => Commentable::INACTIVE,
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
        'seo_noindex' => \N1ebieski\ICore\Casts\Page\SeoNoindexCast::class,
        'seo_nofollow' => \N1ebieski\ICore\Casts\Page\SeoNofollowCast::class,
        'status' => \N1ebieski\ICore\Casts\Page\StatusCast::class,
        'comment' => \N1ebieski\ICore\Casts\Page\CommentCast::class,
        'parent_id' => 'integer',
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
        return \N1ebieski\ICore\Database\Factories\Page\PageFactory::new();
    }

    // Relations

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
     * @return BelongsToMany
     */
    public function ancestors(): BelongsToMany
    {
        return $this->belongsToMany(\N1ebieski\ICore\Models\Page\Page::class, 'pages_closure', 'descendant', 'ancestor');
    }

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function descendants(): BelongsToMany
    {
        return $this->belongsToMany(\N1ebieski\ICore\Models\Page\Page::class, 'pages_closure', 'ancestor', 'descendant');
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function childrens(): HasMany
    {
        return $this->hasMany(\N1ebieski\ICore\Models\Page\Page::class, 'parent_id');
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function childrensRecursiveWithAllRels(): HasMany
    {
        return $this->childrens()->withRecursiveAllRels();
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function stats(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\ICore\Models\Stat\Page\Stat::class,
            'model',
            'stats_values',
            'model_id',
            'stat_id'
        )->withPivot('value');
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

    /**
     * [loadRecursiveAllRels description]
     * @return self [description]
     */
    public function loadRecursiveChildrens(): self
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
    public function loadActiveSiblings(): self
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
        return 'page';
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
        return e(mb_substr(strip_tags($this->replacement_content), 0, 500), false);
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

        // @phpstan-ignore-next-line
        return (!empty($cut[1])) ? $cut[0] . '<a href="' . URL::route('web.page.show', [
                'page' => $this->slug,
                '#more'
            ]) . '">' . Lang::get('icore::pages.more') . '</a>' : $this->replacement_content_html;
    }

    /**
     * [getRealPositionAttribute description]
     * @return int [description]
     */
    public function getRealPositionAttribute(): int
    {
        return $this->position + 1;
    }

    /**
     * [getShortNameAttribute description]
     * @return string [description]
     */
    public function getShortTitleAttribute(): string
    {
        return (strlen($this->title) > 20) ? substr($this->title, 0, 20) : $this->title;
    }

    /**
     * [getFirstImageAttribute description]
     * @return string|null [description]
     */
    public function getFirstImageAttribute(): ?string
    {
        preg_match('/<img.+src=[\'|"](.*?)[\'|"]/', $this->content_html, $image);

        return $image[1] ?? null;
    }

    // Mutators

    /**
     * [setContentAttribute description]
     * @param string $value [description]
     */
    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = !empty($value) ?
            strip_tags(str_replace('[more]', '', $value))
            : null;
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isRedirect(): bool
    {
        return (bool)preg_match('/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/', $this->content ?? '');
    }

    // Scopes

    /**
    * [scopeFilterParent description]
    * @param  Builder $query  [description]
    * @param  mixed  $parent [description]
    * @return Builder|null          [description]
    */
    public function scopeFilterParent(Builder $query, $parent = null): ?Builder
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
    public function scopeWithAncestorsExceptSelf(Builder $query): Builder
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
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::ACTIVE);
    }

    /**
     * [scopeRoot description]
     * @param  Builder $query [description]
     * @return Builder         [description]
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->where('parent_id', null);
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
    public function scopeComponentOnly(Builder $query, array $only = null): ?Builder
    {
        return $query->when($only !== null, function ($query) use ($only) {
            $query->whereIn('id', $only);
        });
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActiveByDate(Builder $query): Builder
    {
        return $query->selectRaw("YEAR(`page`.`created_at`) `year`, MONTH(`page`.`created_at`) `month`, 'pages' AS `type`, COUNT(*) AS `count`")
            ->from("{$this->getTable()} AS page")
            ->where('page.status', Status::ACTIVE)
            ->groupBy('year')
            ->groupBy('month');
    }

    // Factories

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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return PageFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
