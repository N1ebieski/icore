<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\MailingEmailService;
use N1ebieski\ICore\Repositories\MailingEmailRepo;

class MailingEmail extends Model
{
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

    // Relations

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mailing()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\Mailing');
    }

    /**
     * [morph description]
     * @return [type] [description]
     */
    public function morph()
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
    public function scopeUnsent(Builder $query) : Builder
    {
        return $query->where('sent', static::UNSENT);
    }

    // Makers

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
