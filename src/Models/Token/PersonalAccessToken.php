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

namespace N1ebieski\ICore\Models\Token;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\Services\Token\TokenService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Laravel\Sanctum\PersonalAccessToken as BasePersonalAccessToken;

/**
 * N1ebieski\ICore\Models\Token\PersonalAccessToken
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property int|null $symlink_id
 * @property string $name
 * @property string $token
 * @property array|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $expired_at_diff
 * @property-read string $updated_at_diff
 * @property-read PersonalAccessToken|null $symlink
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 * @method static Builder|PersonalAccessToken filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|PersonalAccessToken filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|PersonalAccessToken filterExcept(?array $except = null)
 * @method static Builder|PersonalAccessToken filterOrderBy(?string $orderby = null)
 * @method static Builder|PersonalAccessToken filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|PersonalAccessToken filterReport(?int $report = null)
 * @method static Builder|PersonalAccessToken filterSearch(?string $search = null)
 * @method static Builder|PersonalAccessToken filterStatus(?int $status = null)
 * @method static Builder|PersonalAccessToken newModelQuery()
 * @method static Builder|PersonalAccessToken newQuery()
 * @method static Builder|PersonalAccessToken orderBySearch(string $term)
 * @method static Builder|PersonalAccessToken query()
 * @method static Builder|PersonalAccessToken search(string $term)
 * @method static Builder|PersonalAccessToken whereAbilities($value)
 * @method static Builder|PersonalAccessToken whereCreatedAt($value)
 * @method static Builder|PersonalAccessToken whereExpiredAt($value)
 * @method static Builder|PersonalAccessToken whereId($value)
 * @method static Builder|PersonalAccessToken whereLastUsedAt($value)
 * @method static Builder|PersonalAccessToken whereName($value)
 * @method static Builder|PersonalAccessToken whereSymlinkId($value)
 * @method static Builder|PersonalAccessToken whereToken($value)
 * @method static Builder|PersonalAccessToken whereTokenableId($value)
 * @method static Builder|PersonalAccessToken whereTokenableType($value)
 * @method static Builder|PersonalAccessToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PersonalAccessToken extends BasePersonalAccessToken
{
    use HasFilterable;
    use HasCarbonable;
    use HasWildcardAbilities;
    use HasFullTextSearchable;

    // Configration

    /**
     * @var array
     */
    public static $abilities = [
        'api.*',
        'api.users.*',
        'api.users.view',
        'api.categories.*',
        'api.categories.view',
        'api.tags.*',
        'api.tags.view',
        'api.posts.*',
        'api.posts.view',
        'api.tokens.*',
        'api.tokens.create',
        'api.tokens.delete'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['name'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->fillable[] = 'expired_at';
        $this->casts['expired_at'] = 'datetime';

        parent::__construct($attributes);
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function symlink(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    // Scopes

    /**
     * [scopeFilterStatus description]
     * @param  Builder $query  [description]
     * @param  int|null  $status [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterStatus(Builder $query, int $status = null): ?Builder
    {
        return $query->when($status !== null, function ($query) use ($status) {
            $query->when($status === 1, function ($query) {
                return $query->whereNull('expired_at')
                    ->orWhereDate('expired_at', '>', Carbon::now()->format('Y-m-d'))
                    ->orWhere(function ($query) {
                        $query->whereDate('expired_at', '=', Carbon::now()->format('Y-m-d'))
                            ->whereTime('expired_at', '>', Carbon::now()->format('H:i:s'));
                    });
            }, function ($query) {
                return $query->whereDate('expired_at', '<', Carbon::now()->format('Y-m-d'))
                    ->orWhere(function ($query) {
                        $query->whereDate('expired_at', '=', Carbon::now()->format('Y-m-d'))
                            ->whereTime('expired_at', '<', Carbon::now()->format('H:i:s'));
                    });
            });
        });
    }

    // Accessors

    /**
     * [getExpiredAtDiffAttribute description]
     * @return string [description]
     */
    public function getExpiredAtDiffAttribute(): string
    {
        return Carbon::parse($this->expired_at)->diffForHumans(['parts' => 2]);
    }

    // Factories

    /**
     * [makeService description]
     * @return TokenService [description]
     */
    public function makeService()
    {
        return App::make(TokenService::class, ['token' => $this]);
    }
}
