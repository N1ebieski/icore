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

namespace N1ebieski\ICore\Models\MailingEmail;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use N1ebieski\ICore\ValueObjects\MailingEmail\Sent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Services\MailingEmail\MailingEmailService;
use N1ebieski\ICore\Repositories\MailingEmail\MailingEmailRepo;
use N1ebieski\ICore\Database\Factories\MailingEmail\MailingEmailFactory;

/**
 * @property Sent $sent
 */
class MailingEmail extends Model
{
    use HasFactory;

    // Configuration

    /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
    */
    protected $fillable = ['sent'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mailings_emails';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'sent' => Sent::UNSENT,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'mailing_id' => 'integer',
        'model_id' => 'integer',
        'sent' => \N1ebieski\ICore\Casts\MailingEmail\SentCast::class,
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
        return \N1ebieski\ICore\Database\Factories\MailingEmail\MailingEmailFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function mailing(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\Mailing::class);
    }

    /**
     * Undocumented function
     *
     * @return MorphTo
     */
    public function morph(): MorphTo
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnsent(Builder $query): Builder
    {
        return $query->where('sent', Sent::UNSENT);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return MailingEmailRepo [description]
     */
    public function makeRepo()
    {
        return App::make(MailingEmailRepo::class, ['mailingEmail' => $this]);
    }

    /**
     * [makeService description]
     * @return MailingEmailService [description]
     */
    public function makeService()
    {
        return App::make(MailingEmailService::class, ['mailingEmail' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return MailingEmailFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
