<?php

namespace N1ebieski\ICore\Models;

use N1ebieski\ICore\Models\Role;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Cache\UserCache;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\UserService;
use N1ebieski\ICore\Repositories\UserRepo;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use N1ebieski\ICore\Models\Traits\HasApiTokens;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    use FullTextSearchable;
    use Filterable;
    use Carbonable;
    use PivotEventTrait;

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
    public const WITHOUT_MARKETING = 0;

    /**
     * [public description]
     * @var int
     */
    public const WITH_MARKETING = 1;

    /**
     * [private description]
     * @var bool
     */
    private $pivotEvent = false;

    /**
     * [protected description]
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'marketing',
        'email_verified_at',
        'ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'ip', 'email'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    public $searchable = [
        'name',
        'email',
        'ip'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::ACTIVE,
        'marketing' => 0
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'marketing' => 'integer',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations

    /**
     * [socialites description]
     * @return HasMany [description]
     */
    public function socialites(): HasMany
    {
        return $this->hasMany(\N1ebieski\ICore\Models\Socialite::class);
    }

    /**
     * [posts description]
     * @return HasMany [description]
     */
    public function posts(): HasMany
    {
        return $this->hasMany(\N1ebieski\ICore\Models\Post::class);
    }

    /**
     * [ban description]
     * @return MorphOne [description]
     */
    public function ban(): MorphOne
    {
        return $this->morphOne(\N1ebieski\ICore\Models\BanModel\BanModel::class, 'model');
    }

    /**
     * [emails description]
     * @return MorphMany [description]
     */
    public function emails(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\MailingEmail::class, 'model');
    }

    // Accessors

    /**
     * [getShortNameAttribute description]
     * @return string [description]
     */
    public function getShortNameAttribute(): string
    {
        return (strlen($this->name) > 20) ? substr($this->name, 0, 20) . '...' : $this->name;
    }

    // Scopes

    /**
     * [scopeFilterRole description]
     * @param  Builder $query [description]
     * @param  Role|null  $role  [description]
     * @return Builder|null        [description]
     */
    public function scopeFilterRole(Builder $query, Role $role = null): ?Builder
    {
        return $query->when($role !== null, function ($query) use ($role) {
            $query->role($role->name);
        });
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeMarketing(Builder $query): Builder
    {
        return $query->where('marketing', static::WITH_MARKETING);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', static::ACTIVE);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return UserRepo [description]
     */
    public function makeRepo()
    {
        return App::make(UserRepo::class, ['user' => $this]);
    }

    /**
     * [makeService description]
     * @return UserService [description]
     */
    public function makeService()
    {
        return App::make(UserService::class, ['user' => $this]);
    }

    /**
     * [makeCache description]
     * @return UserCache [description]
     */
    public function makeCache()
    {
        return App::make(UserCache::class, ['user' => $this]);
    }
}
