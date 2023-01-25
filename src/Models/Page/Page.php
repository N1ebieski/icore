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

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Tag\Tag;
use Franzose\ClosureTable\Models\Entity;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\Page\PageCache;
use N1ebieski\ICore\ValueObjects\Page\Status;
use Franzose\ClosureTable\Models\ClosureTable;
use N1ebieski\ICore\Services\Page\PageService;
use N1ebieski\ICore\Models\Traits\HasMultiLang;
use N1ebieski\ICore\Repositories\Page\PageRepo;
use N1ebieski\ICore\ValueObjects\AutoTranslate;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
use N1ebieski\ICore\Models\Traits\HasFixForMultiLangTaggable;
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
 * @property AutoTranslate $auto_translate
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
 * @property PostLang $currentLang
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
 * @method static Builder|Page multiLang()
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
 * @property-read string $less_content_html_attribute
 * @property-read string|null $first_image
 * @mixin \Eloquent
 */
class Page extends Entity
{
    use HasFullTextSearchable;
    use PivotEventTrait;
    use HasCarbonable;
    use HasFactory;
    use HasMultiLang;
    use HasFixForRealDepthClosureTable;
    use HasFixForMultiLangTaggable;
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
        'seo_noindex',
        'seo_nofollow',
        'status',
        'comment',
        'auto_translate',
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'seo_noindex' => SeoNoindex::INACTIVE,
        'seo_nofollow' => SeoNofollow::INACTIVE,
        'status' => Status::INACTIVE,
        'auto_translate' => AutoTranslate::INACTIVE,
        'comment' => Commentable::INACTIVE,
        'icon' => null
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'seo_noindex' => \N1ebieski\ICore\Casts\Page\SeoNoindexCast::class,
        'seo_nofollow' => \N1ebieski\ICore\Casts\Page\SeoNofollowCast::class,
        'status' => \N1ebieski\ICore\Casts\Page\StatusCast::class,
        'comment' => \N1ebieski\ICore\Casts\Page\CommentCast::class,
        'auto_translate' => \N1ebieski\ICore\Casts\AutoTranslateCast::class,
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

    // Attributes

    /**
     *
     * @return Attribute
     */
    public function modelType(): Attribute
    {
        return new Attribute(fn (): string => $this::class);
    }

    /**
     *
     * @return Attribute
     */
    public function poliSelf(): Attribute
    {
        return new Attribute(fn (): string => 'page');
    }

    /**
     *
     * @return Attribute
     */
    public function slug(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->slug);
    }

    /**
     *
     * @return Attribute
     */
    public function title(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->title);
    }

    /**
     *
     * @return Attribute
     */
    public function contentHtml(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->content_html);
    }

    /**
     *
     * @return Attribute
     */
    public function metaTitle(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->meta_title);
    }

    /**
     *
     * @return Attribute
     */
    public function metaDesc(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->meta_desc);
    }

    /**
     *
     * @return Attribute
     */
    public function replacementContent(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->replacement_content);
    }

    /**
     *
     * @return Attribute
     */
    public function shortContent(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->short_content);
    }

    /**
     *
     * @return Attribute
     */
    public function replacementContentHtml(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->replacement_content_html);
    }

    /**
     *
     * @return Attribute
     */
    public function noMoreContentHtml(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->no_more_content_html);
    }

    /**
     *
     * @return Attribute
     */
    public function lessContentHtmlAttribute(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->less_content_html);
    }

    /**
     *
     * @return Attribute
     */
    public function realPosition(): Attribute
    {
        return new Attribute(fn (): int => $this->position + 1);
    }

    /**
     *
     * @return Attribute
     */
    public function shortTitle(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->short_title);
    }

    /**
     *
     * @return Attribute
     */
    public function firstImage(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->first_image);
    }

    /**
     *
     * @return Attribute
     */
    public function content(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->content);
    }

    // Checkers

    /**
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

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
        return $query->when(!is_null($parent), function (Builder $query) use ($parent) {
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
        return $query->with(['ancestors' => function (BelongsToMany|Builder $query) {
            return $query->whereColumn('ancestor', '!=', 'descendant')
                ->orderBy('depth', 'desc')
                ->with('langs');
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
            'childrensRecursiveWithAllRels' => function (HasMany|Builder|Page $query) {
                return $query->selectRaw("`{$this->getTable()}`.*")
                    ->multiLang()
                    ->active()
                    ->orderBy('position', 'asc');
            }
        ]);
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

    /**
     *
     * @param Builder $query
     * @param array $relations
     * @return Builder
     * @throws BindingResolutionException
     */
    public function scopeWithAllRels(Builder $query, array $relations = []): Builder
    {
        return $query->with(array_merge([
            'tags' => function (MorphToMany|Builder|Tag $query) {
                return $query->lang();
            },
            'user',
            App::make(MigrationUtil::class)->contains('create_stats_table') ?
                'stats' : null
        ], $relations));
    }

    // Loads

    /**
     * [loadAncestorsExceptSelf description]
     * @return self [description]
     */
    public function loadAncestorsExceptSelf(): self
    {
        return $this->load(['ancestors' => function (BelongsToMany|Builder $query) {
            return $query->whereColumn('ancestor', '!=', 'descendant')
                ->orderBy('depth', 'desc')
                ->with('langs');
        }]);
    }

    /**
     * [loadRecursiveAllRels description]
     * @return self [description]
     */
    public function loadRecursiveChildrens(): self
    {
        return $this->load([
                'childrensRecursiveWithAllRels' => function (HasMany|Builder|Page $query) {
                    return $query->selectRaw("`{$this->getTable()}`.*")
                        ->multiLang()
                        ->active()
                        ->orderBy('position', 'asc');
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
            $this->selectRaw("`{$this->getTable()}`.*")
                ->multiLang()
                ->active()
                ->where('parent_id', $this->parent_id)
                ->orderBy('position', 'asc')
                ->get()
        );
    }

    /**
     *
     * @param array $relations
     * @return $this
     * @throws BindingResolutionException
     */
    public function loadAllRels(array $relations = [])
    {
        return $this->load(array_merge([
            'tags' => function (MorphToMany|Builder|Tag $query) {
                return $query->lang();
            },
            'user',
            App::make(MigrationUtil::class)->contains('create_stats_table') ?
                'stats' : null
        ], $relations));
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
