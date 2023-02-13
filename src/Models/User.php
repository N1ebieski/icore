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

use N1ebieski\ICore\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\ValueObjects\Lang;
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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use N1ebieski\ICore\Http\Resources\User\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use N1ebieski\ICore\Database\Factories\User\UserFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * N1ebieski\ICore\Models\User
 *
 * @property string $name
 * @property Status $status
 * @property Marketing $marketing
 * @property Lang $pref_lang
 * @method \N1ebieski\ICore\Models\Token\PersonalAccessToken currentAccessToken()
 * @property int $id
 * @property string|null $ip
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \N1ebieski\ICore\Models\BanModel\BanModel|null $ban
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\MailingEmail\MailingEmail[] $emails
 * @property-read int|null $emails_count
 * @property-read string $created_at_diff
 * @property-read string $short_name
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Socialite[] $socialites
 * @property-read int|null $socialites_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Token\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User active()
 * @method static \N1ebieski\ICore\Database\Factories\User\UserFactory factory(...$parameters)
 * @method static Builder|User filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|User filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|User filterExcept(?array $except = null)
 * @method static Builder|User filterOrderBy(?string $orderby = null)
 * @method static Builder|User filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|User filterReport(?int $report = null)
 * @method static Builder|User filterRole(?\N1ebieski\ICore\Models\Role $role = null)
 * @method static Builder|User filterSearch(?string $search = null)
 * @method static Builder|User filterStatus(?int $status = null)
 * @method static Builder|User marketing()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User orderBySearch(string $term)
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User search(string $term)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIp($value)
 * @method static Builder|User whereMarketing($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
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
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'marketing',
        'pref_lang',
        'email_verified_at',
        'ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token', 'ip', 'email'
    ];

    /**
     * The columns of the full text index
     *
     * @var array<string>
     */
    public $searchable = [
        'name',
        'email',
        'ip'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, int>
     */
    protected $attributes = [
        'status' => Status::ACTIVE,
        'marketing' => Marketing::INACTIVE
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'status' => \N1ebieski\ICore\Casts\User\StatusCast::class,
        'marketing' => \N1ebieski\ICore\Casts\User\MarketingCast::class,
        'pref_lang' => \N1ebieski\ICore\Casts\LangCast::class,
        'email_verified_at' => 'datetime',
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
        $this->attributes['pref_lang'] = Config::get('app.locale');

        parent::__construct($attributes);
    }

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

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function shortName(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\User\ShortName::class, [
            'user' => $this
        ])();
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
        return $query->when(!is_null($role), function (Builder $query) use ($role) {
            /** @phpstan-ignore-next-line */
            return $query->role($role->name);
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

    /**
     * [makeResource description]
     * @return UserResource [description]
     */
    public function makeResource()
    {
        return App::make(UserResource::class, ['user' => $this]);
    }
}
