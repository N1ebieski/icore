<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\ICore\Models\Traits\Filterable;
use Carbon\Carbon;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Repositories\UserRepo;

/**
 * [User description]
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasRoles, FullTextSearchable, Filterable;

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
        'name', 'email', 'password', 'status', 'email_verified_at', 'ip'
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
    protected $searchable = [
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
        'status' => 1,
    ];

    // Relations

    /**
     * [socialites description]
     * @return HasMany [description]
     */
    public function socialites() : HasMany
    {
        return $this->hasMany('N1ebieski\ICore\Models\Socialite');
    }

    /**
     * [posts description]
     * @return HasMany [description]
     */
    public function posts() : HasMany
    {
        return $this->hasMany('N1ebieski\ICore\Models\Post');
    }

    /**
     * [ban description]
     * @return MorphOne [description]
     */
    public function ban() : MorphOne
    {
        return $this->morphOne('N1ebieski\ICore\Models\BanModel\BanModel', 'model');
    }

    /**
     * [emails description]
     * @return MorphMany [description]
     */
    public function emails() : MorphMany
    {
        return $this->morphMany('N1ebieski\ICore\Models\MailingEmail', 'model');
    }

    // Accessors

    /**
     * [getShortNameAttribute description]
     * @return string [description]
     */
    public function getShortNameAttribute() : string
    {
        return (strlen($this->name) > 20) ? substr($this->name, 0, 20).'...' : $this->name;
    }

    /**
     * [getCreatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getCreatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * [getUpdatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getUpdatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }

    // Scopes

    /**
     * [scopeFilterRole description]
     * @param  Builder $query [description]
     * @param  Role|null  $role  [description]
     * @return Builder|null        [description]
     */
    public function scopeFilterRole(Builder $query, Role $role = null) : ?Builder
    {
        return $query->when($role !== null, function($query) use ($role) {
            $query->role($role->name);
        });
    }

    // Makers

    /**
     * [makeRepo description]
     * @return UserRepo [description]
     */
    public function makeRepo() 
    {
        return app()->make(UserRepo::class, ['user' => $this]);
    }
}
