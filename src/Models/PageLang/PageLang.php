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

namespace N1ebieski\ICore\Models\PageLang;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Services\PageLang\PageLangService;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Database\Factories\PageLang\PageLangFactory;

/**
 * N1ebieski\ICore\Models\PageLang\PageLang
 *
 * @property int $id
 * @property int $page_id
 * @property string $slug
 * @property string $title
 * @property string $content_html
 * @property string|null $content
 * @property string|null $seo_title
 * @property string|null $seo_desc
 * @property \N1ebieski\ICore\ValueObjects\Progress $progress
 * @property \N1ebieski\ICore\ValueObjects\Lang $lang
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string|null $first_image
 * @property-read string $less_content_html
 * @property-read string $meta_desc
 * @property-read string $meta_title
 * @property-read string $no_more_content_html
 * @property-read \N1ebieski\ICore\Models\Page\Page $page
 * @property-read string $replacement_content
 * @property-read string $replacement_content_html
 * @property-read string $short_content
 * @property-read string $short_title
 * @property-read string $updated_at_diff
 * @method static \N1ebieski\ICore\Database\Factories\PageLang\PageLangFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang orderBySearch(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereSeoDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageLang withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class PageLang extends Model
{
    use Sluggable;
    use HasFullTextSearchable;
    use HasCarbonable;
    use HasFactory;

    // Configuration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pages_langs';

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['page'];

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
        'seo_title',
        'seo_desc',
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
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \N1ebieski\ICore\Database\Factories\PageLang\PageLangFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\PageLang\PageLangFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\Page\Page::class);
    }

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function content(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\Content::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function contentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\ContentHtml::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function metaTitle(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\MetaTitle::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function metaDesc(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\MetaDesc::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\ReplacementContent::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function shortContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\ShortContent::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\ReplacementContentHtml::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function noMoreContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\NoMoreContentHtml::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function lessContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\LessContentHtml::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function firstImage(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\FirstImage::class, [
            'pageLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function shortTitle(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PageLang\ShortTitle::class, [
            'pageLang' => $this
        ])();
    }

    // Factories

    /**
     *
     * @return PageLangService
     */
    public function makeService()
    {
        return App::make(PageLangService::class, ['pageLang' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return PageLangFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
