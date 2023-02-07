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

namespace N1ebieski\ICore\Models\MailingLang;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\ICore\Services\MailingLang\MailingLangService;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Database\Factories\MailingLang\MailingLangFactory;

/**
 * N1ebieski\ICore\Models\MailingLang\MailingLang
 *
 * @property int $id
 * @property int $mailing_id
 * @property string $title
 * @property string $content_html
 * @property string|null $content
 * @property \N1ebieski\ICore\ValueObjects\Progress $progress
 * @property \N1ebieski\ICore\ValueObjects\Lang $lang
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read \N1ebieski\ICore\Models\Mailing $mailing
 * @property-read string $replacement_content
 * @property-read string $replacement_content_html
 * @property-read string $short_content
 * @property-read string $updated_at_diff
 * @method static \N1ebieski\ICore\Database\Factories\MailingLang\MailingLangFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang orderBySearch(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereMailingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingLang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailingLang extends Model
{
    use HasFullTextSearchable;
    use HasCarbonable;
    use HasFactory;

    // Configuration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mailings_langs';

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['mailing'];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'content'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'content_html',
        'content',
        'lang',
        'progress'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'lang' => \N1ebieski\ICore\Casts\LangCast::class,
        'progress' => \N1ebieski\ICore\Casts\ProgressCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'progress' => 100
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes['lang'] = Config::get('app.locale');

        parent::__construct($attributes);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \N1ebieski\ICore\Database\Factories\MailingLang\MailingLangFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\MailingLang\MailingLangFactory::new();
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

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function content(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\MailingLang\Content::class, [
            'mailingLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function contentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\MailingLang\ContentHtml::class, [
            'mailingLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\MailingLang\ReplacementContent::class, [
            'mailingLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function shortContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\MailingLang\ShortContent::class, [
            'mailingLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\MailingLang\ReplacementContentHtml::class, [
            'mailingLang' => $this
        ])();
    }

    // Factories

    /**
     *
     * @return MailingLangService
     */
    public function makeService()
    {
        return App::make(MailingLangService::class, ['mailingLang' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return MailingLangFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
