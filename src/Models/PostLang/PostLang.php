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

namespace N1ebieski\ICore\Models\PostLang;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Services\PostLang\PostLangService;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Database\Factories\PostLang\PostLangFactory;

/**
 * N1ebieski\ICore\Models\PostLang\PostLang
 *
 * @property int $id
 * @property int $post_id
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
 * @property-read \N1ebieski\ICore\Models\Post $post
 * @property-read string $replacement_content
 * @property-read string $replacement_content_html
 * @property-read string $short_content
 * @property-read string $updated_at_diff
 * @method static \N1ebieski\ICore\Database\Factories\PostLang\PostLangFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang orderBySearch(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereSeoDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostLang withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class PostLang extends Model
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
    protected $table = 'posts_langs';

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['post'];

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
     * @return \N1ebieski\ICore\Database\Factories\PostLang\PostLangFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\PostLang\PostLangFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\Post::class);
    }

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function content(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\Content::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function contentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\ContentHtml::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function metaTitle(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\MetaTitle::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function metaDesc(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\MetaDesc::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\ReplacementContent::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function shortContent(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\ShortContent::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function replacementContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\ReplacementContentHtml::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function noMoreContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\NoMoreContentHtml::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function lessContentHtml(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\LessContentHtml::class, [
            'postLang' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function firstImage(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\PostLang\FirstImage::class, [
            'postLang' => $this
        ])();
    }

    // Factories

    /**
     *
     * @return PostLangService
     */
    public function makeService()
    {
        return App::make(PostLangService::class, ['postLang' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return PostLangFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
