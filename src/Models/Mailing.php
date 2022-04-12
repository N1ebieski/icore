<?php

namespace N1ebieski\ICore\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Services\MailingService;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Repositories\MailingRepo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mailing extends Model
{
    use FullTextSearchable;
    use Filterable;
    use Carbonable;
    use HasFactory;

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
     * [public description]
     * @var int
     */
    public const INPROGRESS = 3;

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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'activation_at' => 'datetime',
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
        return \N1ebieski\ICore\Database\Factories\Mailing\MailingFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function emails(): HasMany
    {
        return $this->hasMany(\N1ebieski\ICore\Models\MailingEmail::class);
    }

    // Accessors

    /**
     * [getProgressSuccessAttribute description]
     * @return int|null [description]
     */
    public function getProgressSuccessAttribute(): ?int
    {
        return ($this->emails->isNotEmpty()) ?
            (int)round(($this->emails->where('sent', 1)->count() / $this->emails->count()) * 100, 0)
            : null;
    }

    /**
     * [getProgressFailedAttribute description]
     * @return int|null [description]
     */
    public function getProgressFailedAttribute(): ?int
    {
        return ($this->emails->isNotEmpty()) ?
            (int)round(($this->emails->where('sent', 2)->count() / $this->emails->count()) * 100, 0)
            : null;
    }

    /**
     * [getActivationAtDiffAttribute description]
     * @return string [description]
     */
    public function getActivationAtDiffAttribute(): string
    {
        return ($this->activation_at != null) ?
            Carbon::parse($this->activation_at)->diffForHumans(['parts' => 2])
            : '';
    }

    /**
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute(): string
    {
         return Purifier::clean($this->attributes['content_html']);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getReplacementContentHtmlAttribute(): string
    {
        return App::make(\N1ebieski\ICore\Utils\Conversions\Replacement::class)
            ->handle($this->content_html, function ($value) {
                return $value;
            });
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getReplacementContentAttribute(): string
    {
        return App::make(\N1ebieski\ICore\Utils\Conversions\Replacement::class)
            ->handle($this->content, function ($value) {
                return $value;
            });
    }

    /**
     * [getShortContentAttribute description]
     * @return string [description]
     */
    public function getShortContentAttribute(): string
    {
        return mb_substr(strip_tags($this->replacement_content), 0, 300);
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
        return $query->where('status', static::ACTIVE);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeProgress(Builder $query): Builder
    {
        return $query->where('status', static::INPROGRESS);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', static::SCHEDULED);
    }

    // Mutators

    /**
     * [setActivationAtAttribute description]
     * @param [type] $value [description]
     */
    public function setActivationAtAttribute($value): void
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
    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = !empty($value) ?
            strip_tags(str_replace('[more]', '', $value))
            : null;
    }

    // Factories

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
