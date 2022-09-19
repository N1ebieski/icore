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

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Exceptions\InvalidFormatException;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Services\Mailing\MailingService;
use N1ebieski\ICore\Repositories\Mailing\MailingRepo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Database\Factories\Mailing\MailingFactory;

/**
 * N1ebieski\ICore\Models\Mailing
 *
 * @property Status $status
 * @property int $id
 * @property string $title
 * @property string $content_html
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $activation_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\MailingEmail\MailingEmail[] $emails
 * @property-read int|null $emails_count
 * @property-read string $activation_at_diff
 * @property-read string $created_at_diff
 * @property-read int|null $progress_failed
 * @property-read int|null $progress_success
 * @property-read string $replacement_content
 * @property-read string $replacement_content_html
 * @property-read string $short_content
 * @property-read string $updated_at_diff
 * @method static Builder|Mailing active()
 * @method static \N1ebieski\ICore\Database\Factories\Mailing\MailingFactory factory(...$parameters)
 * @method static Builder|Mailing filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Mailing filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Mailing filterExcept(?array $except = null)
 * @method static Builder|Mailing filterOrderBy(?string $orderby = null)
 * @method static Builder|Mailing filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Mailing filterReport(?int $report = null)
 * @method static Builder|Mailing filterSearch(?string $search = null)
 * @method static Builder|Mailing filterStatus(?int $status = null)
 * @method static Builder|Mailing newModelQuery()
 * @method static Builder|Mailing newQuery()
 * @method static Builder|Mailing orderBySearch(string $term)
 * @method static Builder|Mailing progress()
 * @method static Builder|Mailing query()
 * @method static Builder|Mailing scheduled()
 * @method static Builder|Mailing search(string $term)
 * @method static Builder|Mailing whereActivationAt($value)
 * @method static Builder|Mailing whereContent($value)
 * @method static Builder|Mailing whereContentHtml($value)
 * @method static Builder|Mailing whereCreatedAt($value)
 * @method static Builder|Mailing whereId($value)
 * @method static Builder|Mailing whereStatus($value)
 * @method static Builder|Mailing whereTitle($value)
 * @method static Builder|Mailing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Mailing extends Model
{
    use HasFullTextSearchable;
    use HasFilterable;
    use HasCarbonable;
    use HasFactory;

    // Configuration

    /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
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
        'status' => Status::INACTIVE,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'status' => \N1ebieski\ICore\Casts\Mailing\StatusCast::class,
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
        return $this->hasMany(\N1ebieski\ICore\Models\MailingEmail\MailingEmail::class);
    }

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function progressSuccess(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ProgressSuccess::class, [
            'mailing' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function progressFailed(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ProgressFailed::class, [
            'mailing' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function activationAtDiff(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ActivationAtDiff::class, [
            'mailing' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function contentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ContentHtml::class, [
            'mailing' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ReplacementContentHtml::class, [
            'mailing' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ReplacementContent::class, [
            'mailing' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function shortContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ShortContent::class, [
            'mailing' => $this
        ])();
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

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeProgress(Builder $query): Builder
    {
        return $query->where('status', Status::INPROGRESS);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', Status::SCHEDULED);
    }

    // Mutators

    /**
     *
     * @param null|string $value
     * @return void
     * @throws InvalidFormatException
     */
    public function setActivationAtAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['activation_at'] = null;
            return;
        }

        $this->attributes['activation_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     *
     * @param null|string $value
     * @return void
     */
    public function setContentAttribute(?string $value): void
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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return MailingFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
