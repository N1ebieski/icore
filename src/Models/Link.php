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

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\Link\LinkCache;
use N1ebieski\ICore\ValueObjects\Link\Type;
use N1ebieski\ICore\Services\Link\LinkService;
use N1ebieski\ICore\Repositories\Link\LinkRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\Models\Traits\HasPositionable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Database\Factories\Link\LinkFactory;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * N1ebieski\ICore\Models\Link
 *
 * @property Type $type
 * @property int $id
 * @property string $url
 * @property string $name
 * @property string|null $img_url
 * @property bool $home
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\N1ebieski\ICore\Models\Category\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read string $created_at_diff
 * @property-read string|null $img_url_from_storage
 * @property-read string $link_as_html
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|Link[] $siblings
 * @property-read int|null $siblings_count
 * @method static \N1ebieski\ICore\Database\Factories\Link\LinkFactory factory(...$parameters)
 * @method static Builder|Link filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Link filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Link filterExcept(?array $except = null)
 * @method static Builder|Link filterOrderBy(?string $orderby = null)
 * @method static Builder|Link filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Link filterReport(?int $report = null)
 * @method static Builder|Link filterSearch(?string $search = null)
 * @method static Builder|Link filterStatus(?int $status = null)
 * @method static Builder|Link filterType(?string $type = null)
 * @method static Builder|Link newModelQuery()
 * @method static Builder|Link newQuery()
 * @method static Builder|Link query()
 * @method static Builder|Link whereCreatedAt($value)
 * @method static Builder|Link whereHome($value)
 * @method static Builder|Link whereId($value)
 * @method static Builder|Link whereImgUrl($value)
 * @method static Builder|Link whereName($value)
 * @method static Builder|Link wherePosition($value)
 * @method static Builder|Link whereType($value)
 * @method static Builder|Link whereUpdatedAt($value)
 * @method static Builder|Link whereUrl($value)
 * @mixin \Eloquent
 */
class Link extends Model
{
    use HasPositionable;
    use HasCarbonable;
    use HasFilterable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type',
        'url',
        'name',
        'img_url',
        'home',
        'position'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'type' => \N1ebieski\ICore\Casts\Link\TypeCast::class,
        'home' => 'boolean',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Undocumented variable
     *
     * @var string
     */
    public $path = 'vendor/icore/links';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\Link\LinkFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function siblings(): HasMany
    {
        return $this->hasMany(\N1ebieski\ICore\Models\Link::class, 'type', 'type');
    }

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

    // Scopes

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterType(Builder $query, string $type = null): ?Builder
    {
        return $query->when($type !== null, function ($query) use ($type) {
            $query->where('type', $type);
        });
    }

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function imgUrlFromStorage(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Link\ImgUrlFromStorage::class, [
            'link' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function linkAsHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Link\LinkAsHtml::class, [
            'link' => $this
        ])();
    }

    // Loads

    /**
     * [loadAncestorsWithoutSelf description]
     * @return self [description]
     */
    public function loadAncestorsWithoutSelf(): self
    {
        return $this->load(['categories' => function ($query) {
            $query->withAncestorsExceptSelf();
        }]);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return LinkRepo [description]
     */
    public function makeRepo()
    {
        return App::make(LinkRepo::class, ['link' => $this]);
    }

    /**
     * [makeCache description]
     * @return LinkCache [description]
     */
    public function makeCache()
    {
        return App::make(LinkCache::class, ['link' => $this]);
    }

    /**
     * [makeService description]
     * @return LinkService [description]
     */
    public function makeService()
    {
        return App::make(LinkService::class, ['link' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return LinkFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
