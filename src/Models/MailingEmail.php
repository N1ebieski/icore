<?php

namespace N1ebieski\ICore\Models;

use N1ebieski\ICore\Models\Mailing;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\MailingEmailService;
use N1ebieski\ICore\Repositories\MailingEmailRepo;

/**
 * [MailingEmail description]
 */
class MailingEmail extends Model
{

    // Configuration

    /**
     * [private description]
     * @var Mailing
     */
    protected $mailing;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['send'];

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
        'send' => 0,
    ];

    // Setters

    /**
     * @param Mailing $mailing
     *
     * @return static
     */
    public function setMailing(Mailing $mailing)
    {
        $this->mailing = $mailing;

        return $this;
    }

    // Getters

    public function getMailing() : Mailing
    {
        return $this->mailing;
    }

    // Relations

    /**
     * [mailing description]
     * @return [type] [description]
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

    // Makers

    /**
     * [makeRepo description]
     * @return MailingEmailRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(MailingEmailRepo::class, ['mailingEmail' => $this]);
    }

    /**
     * [makeService description]
     * @return MailingEmailService [description]
     */
    public function makeService()
    {
        return app()->make(MailingEmailService::class, ['mailingEmail' => $this]);
    }
}
