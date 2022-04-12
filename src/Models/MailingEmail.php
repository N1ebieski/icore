<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\MailingEmailService;
use N1ebieski\ICore\Repositories\MailingEmailRepo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MailingEmail extends Model
{
    use HasFactory;

    // Configuration

    /**
     * [public description]
     * @var int
     */
    public const SENT = 1;

    /**
     * [public description]
     * @var int
     */
    public const UNSENT = 0;

    /**
     * [public description]
     * @var int
     */
    public const ERROR = 2;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
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
        'sent' => self::UNSENT,
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
        'sent' => 'integer',
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
        return $query->where('sent', static::UNSENT);
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
}
