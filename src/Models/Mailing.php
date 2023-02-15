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

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\HasMultiLang;
use N1ebieski\ICore\ValueObjects\AutoTranslate;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Models\MailingLang\MailingLang;
use N1ebieski\ICore\Services\Mailing\MailingService;
use N1ebieski\ICore\Repositories\Mailing\MailingRepo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\ICore\Models\Interfaces\AutoTranslateInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Database\Factories\Mailing\MailingFactory;

/**
 * N1ebieski\ICore\Models\Mailing
 *
 * @property Status $status
 * @property AutoTranslate $auto_translate
 * @property int $id
 * @property string $title
 * @property string|null $content_html
 * @property string|null $content
 * @property MailingLang $currentLang
 * @property \Illuminate\Support\Carbon|null $activation_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|MailingLang[] $langs
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\MailingEmail\MailingEmail[] $emails
 * @property-read int $emails_count
 * @property-read int $emails_success_count
 * @property-read int $emails_failed_count
 * @property-read string $activation_at_diff
 * @property-read string $created_at_diff
 * @property-read int|null $progress_failed
 * @property-read int|null $progress_success
 * @property-read string $replacement_content
 * @property-read string $replacement_content_html
 * @property-read string $short_content
 * @property-read string $updated_at_diff
 * @method static Builder|Mailing multiLang()
 * @method static Builder|Mailing autoTrans()
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
class Mailing extends Model implements AutoTranslateInterface
{
    use HasFullTextSearchable;
    use HasFilterable;
    use HasCarbonable;
    use HasFactory;
    use HasMultiLang;

    // Configuration

    /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
    */
    protected $fillable = [
        'status',
        'auto_translate',
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
        'auto_translate' => AutoTranslate::INACTIVE,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'status' => \N1ebieski\ICore\Casts\Mailing\StatusCast::class,
        'auto_translate' => \N1ebieski\ICore\Casts\AutoTranslateCast::class,
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
    public function activationAt(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Mailing\ActivationAt::class, [
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
     */
    public function title(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->title);
    }

    /**
     *
     * @return Attribute
     */
    public function content(): Attribute
    {
        return new Attribute(fn (): ?string => $this->currentLang->content);
    }

    /**
     *
     * @return Attribute
     */
    public function contentHtml(): Attribute
    {
        return new Attribute(fn (): ?string => $this->currentLang->content_html);
    }

    /**
     *
     * @return Attribute
     */
    public function replacementContentHtml(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->replacement_content_html);
    }

    /**
     *
     * @return Attribute
     */
    public function replacementContent(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->replacement_content);
    }

    /**
     *
     * @return Attribute
     */
    public function shortContent(): Attribute
    {
        // @phpstan-ignore-next-line
        return new Attribute(fn (): ?string => $this->currentLang->short_content);
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
    public function makeService(): MailingService
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
