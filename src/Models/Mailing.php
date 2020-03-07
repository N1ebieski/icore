<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\ICore\Models\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Mews\Purifier\Facades\Purifier;
use N1ebieski\ICore\Repositories\MailingRepo;
use N1ebieski\ICore\Services\MailingService;

/**
 * [Mailing description]
 */
class Mailing extends Model
{
    use FullTextSearchable, Filterable;

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
     * [public description]
     * @var int
     */
    public const SCHEDULED = 2;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'title',
        'content',
        'content_html',
        'status',
        'activation_at'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'content',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::INACTIVE,
    ];

    // Relations

    /**
     * [emails description]
     * @return [type] [description]
     */
    public function emails()
    {
        return $this->hasMany('N1ebieski\ICore\Models\MailingEmail');
    }

    // Accessors

    /**
     * [getProgressSuccessAttribute description]
     * @return int|null [description]
     */
    public function getProgressSuccessAttribute() : ?int
    {
        return ($this->emails->isNotEmpty()) ?
            (int)round(($this->emails->where('sent', 1)->count()/$this->emails->count())*100, 0)
            : null;
    }

    /**
     * [getProgressFailedAttribute description]
     * @return int|null [description]
     */
    public function getProgressFailedAttribute() : ?int
    {
        return ($this->emails->isNotEmpty()) ?
            (int)round(($this->emails->where('sent', 2)->count()/$this->emails->count())*100, 0)
            : null;
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

    /**
     * [getActivationAtDiffAttribute description]
     * @return string [description]
     */
    public function getActivationAtDiffAttribute() : string
    {
        return ($this->activation_at != null) ? Carbon::parse($this->activation_at)->diffForHumans() : '';
    }

    /**
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute() : string
    {
         return Purifier::clean($this->attributes['content_html']);
    }

    /**
     * [getShortContentAttribute description]
     * @return string [description]
     */
    public function getShortContentAttribute() : string
    {
        return mb_substr($this->content, 0, 300);
    }

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where('status', static::ACTIVE);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduled(Builder $query) : Builder
    {
        return $query->where('status', static::SCHEDULED);
    }

    // Mutators

    /**
     * [setActivationAtAttribute description]
     * @param [type] $value [description]
     */
    public function setActivationAtAttribute($value) : void
    {
        if ($value === null) {
            $this->attributes['activation_at'] = null;
            return;
        }

        $this->attributes['activation_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * [setContentAttribute description]
     * @param [type] $value [description]
     */
    public function setContentAttribute($value) : void
    {
        $this->attributes['content'] = strip_tags(str_replace('[more]', '', $value));
    }

    // Makers

    /**
     * [makeRepo description]
     * @return MailingRepo [description]
     */
    public function makeRepo()
    {
        return App::make(MailingRepo::class, ['mailing' => $this]);
    }

    /**
     * [makeService description]
     * @return MailingService [description]
     */
    public function makeService()
    {
        return App::make(MailingService::class, ['mailing' => $this]);
    }
}
