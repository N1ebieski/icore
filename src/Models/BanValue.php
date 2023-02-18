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
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\HasLang;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\Cache\BanValue\BanValueCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Repositories\BanValue\BanValueRepo;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\ICore\Database\Factories\BanValue\BanValueFactory;

/**
 * N1ebieski\ICore\Models\BanValue
 *
 * @property Type $type
 * @property int $id
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $updated_at_diff
 * @method static Builder|BanValue lang()
 * @method static \N1ebieski\ICore\Database\Factories\BanValue\BanValueFactory factory(...$parameters)
 * @method static Builder|BanValue filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|BanValue filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|BanValue filterExcept(?array $except = null)
 * @method static Builder|BanValue filterOrderBy(?string $orderby = null)
 * @method static Builder|BanValue filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|BanValue filterReport(?int $report = null)
 * @method static Builder|BanValue filterSearch(?string $search = null)
 * @method static Builder|BanValue filterStatus(?int $status = null)
 * @method static Builder|BanValue filterType(?string $type = null)
 * @method static Builder|BanValue newModelQuery()
 * @method static Builder|BanValue newQuery()
 * @method static Builder|BanValue orderBySearch(string $term)
 * @method static Builder|BanValue query()
 * @method static Builder|BanValue search(string $term)
 * @method static Builder|BanValue whereCreatedAt($value)
 * @method static Builder|BanValue whereId($value)
 * @method static Builder|BanValue whereType($value)
 * @method static Builder|BanValue whereUpdatedAt($value)
 * @method static Builder|BanValue whereValue($value)
 * @mixin \Eloquent
 */
class BanValue extends Model
{
    use HasFilterable;
    use HasFullTextSearchable;
    use HasCarbonable;
    use HasFactory;
    use HasLang;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type', 'value', 'lang'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['value'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bans_values';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'type' => \N1ebieski\ICore\Casts\BanValue\TypeCast::class,
        'lang' => \N1ebieski\ICore\Casts\LangCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes['lang'] = Config::get('app.locale');

        parent::__construct($attributes);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\BanValue\BanValueFactory::new();
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
        return $query->when(!is_null($type), function (Builder $query) use ($type) {
            return $query->where('type', $type);
        });
    }

    // Factories

    /**
     * [makeRepo description]
     * @return BanValueRepo [description]
     */
    public function makeRepo()
    {
        return App::make(BanValueRepo::class, ['banvalue' => $this]);
    }

    /**
     * [makeCache description]
     * @return BanValueCache [description]
     */
    public function makeCache()
    {
        return App::make(BanValueCache::class, ['banvalue' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return BanValueFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
