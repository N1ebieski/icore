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
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Services\Socialite\SocialiteService;
use N1ebieski\ICore\Repositories\Socialite\SocialiteRepo;
use N1ebieski\ICore\Database\Factories\Socialite\SocialiteFactory;

/**
 * N1ebieski\ICore\Models\Socialite
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $provider_name
 * @property string|null $provider_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $updated_at_diff
 * @property-read \N1ebieski\ICore\Models\User $user
 * @method static \N1ebieski\ICore\Database\Factories\Socialite\SocialiteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite whereProviderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Socialite whereUserId($value)
 * @mixin \Eloquent
 */
class Socialite extends Model
{
    use HasCarbonable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id', 'provider_name', 'provider_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = ['provider_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
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
        return SocialiteFactory::new();
    }

    // Relations

    /**
     * [user description]
     * @return BelongsTo [description]
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\User::class);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return SocialiteRepo [description]
     */
    public function makeRepo()
    {
        return App::make(SocialiteRepo::class, ['socialite' => $this]);
    }

    /**
     * [makeService description]
     * @return SocialiteService [description]
     */
    public function makeService()
    {
        return App::make(SocialiteService::class, ['socialite' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return SocialiteFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
