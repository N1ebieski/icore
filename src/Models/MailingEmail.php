<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * [MailingEmail description]
 */
class MailingEmail extends Model
{

    // Configuration

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
        return $this->morphTo('model');
    }
}
