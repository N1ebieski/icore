<?php

namespace N1ebieski\ICore\Models;

use N1ebieski\ICore\Models\Role;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\User\UserCache;
use N1ebieski\ICore\ValueObjects\User\Status;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use N1ebieski\ICore\Services\User\UserService;
use N1ebieski\ICore\Models\Traits\HasApiTokens;
use N1ebieski\ICore\Repositories\User\UserRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\ValueObjects\User\Marketing;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use N1ebieski\ICore\Database\Factories\User\UserFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;

/**
 * @property Status $status
 * @property Marketing $marketing
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    use HasFullTextSearchable;
    use HasFilterable;
    use HasCarbonable;
    use PivotEventTrait;
    use HasFactory;

    // Configuration

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
        'status' => Status::ACTIVE,
        'marketing' => Marketing::INACTIVE
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => \N1ebieski\ICore\Casts\User\StatusCast::class,
        'marketing' => \N1ebieski\ICore\Casts\User\MarketingCast::class,
        'email_verified_at' => 'datetime',
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
        return \N1ebieski\ICore\Database\Factories\User\UserFactory::new();
    }

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
        return $this->morphMany(\N1ebieski\ICore\Models\MailingEmail\MailingEmail::class, 'model');
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
        return $query->where('marketing', Marketing::ACTIVE);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::ACTIVE);
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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return UserFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
