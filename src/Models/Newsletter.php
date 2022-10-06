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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\NewsletterToken;
use Illuminate\Database\Eloquent\Relations\HasOne;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\Newsletter\NewsletterFactory;

/**
 * N1ebieski\ICore\Models\Newsletter
 *
 * @property Status $status
 * @property NewsletterToken $token
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Newsletter active()
 * @method static \N1ebieski\ICore\Database\Factories\Newsletter\NewsletterFactory factory(...$parameters)
 * @method static Builder|Newsletter newModelQuery()
 * @method static Builder|Newsletter newQuery()
 * @method static Builder|Newsletter query()
 * @method static Builder|Newsletter whereCreatedAt($value)
 * @method static Builder|Newsletter whereEmail($value)
 * @method static Builder|Newsletter whereId($value)
 * @method static Builder|Newsletter whereStatus($value)
 * @method static Builder|Newsletter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Newsletter extends Model
{
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['email', 'status'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = ['email'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => Status::ACTIVE,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'status' => \N1ebieski\ICore\Casts\Newsletter\StatusCast::class,
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
        return \N1ebieski\ICore\Database\Factories\Newsletter\NewsletterFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function token(): HasOne
    {
        return $this->hasOne(NewsletterToken::class, 'email', 'email');
    }

    // Scopes

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
     * Undocumented function
     *
     * @param mixed $parameters
     * @return NewsletterFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
