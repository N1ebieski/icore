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

use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\NewsletterToken\NewsletterTokenFactory;

/**
 * N1ebieski\ICore\Models\NewsletterToken
 *
 * @property string $token
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Newsletter $newsletter
 * @method static \N1ebieski\ICore\Database\Factories\NewsletterToken\NewsletterTokenFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsletterToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NewsletterToken extends Model
{
    use HasFactory;

    // Configuration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'newsletters_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['email', 'token', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = ['token', 'email'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
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
        return NewsletterTokenFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class, 'email', 'email');
    }

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return NewsletterTokenFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
