<?php

namespace N1ebieski\ICore\Models\Token;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\TokenService;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Laravel\Sanctum\PersonalAccessToken as BasePersonalAccessToken;

class PersonalAccessToken extends BasePersonalAccessToken
{
    use Filterable;
    use Carbonable;
    use WildcardAbilities;
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
