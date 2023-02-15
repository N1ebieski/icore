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

namespace N1ebieski\ICore\Models\CategoryLang;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\ICore\Models\Interfaces\TransableInterface;
use N1ebieski\ICore\Services\CategoryLang\CategoryLangService;
use N1ebieski\ICore\Http\Resources\CategoryLang\CategoryLangResource;
use N1ebieski\ICore\Database\Factories\CategoryLang\CategoryLangFactory;

/**
 * N1ebieski\ICore\Models\CategoryLang\CategoryLang
 *
 * @property int $id
 * @property int $category_id
 * @property string $slug
 * @property string $name
 * @property \N1ebieski\ICore\ValueObjects\Lang $lang
 * @property \N1ebieski\ICore\ValueObjects\Progress $progress
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \N1ebieski\ICore\Models\Category\Category $category
 * @property-read string $created_at_diff
 * @property-read string $updated_at_diff
 * @method static \N1ebieski\ICore\Database\Factories\CategoryLang\CategoryLangFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang orderBySearch(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryLang withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class CategoryLang extends Model implements TransableInterface
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
    protected $table = 'categories_langs';

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['category'];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'lang', 'progress', 'translated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'lang' => \N1ebieski\ICore\Casts\LangCast::class,
        'progress' => \N1ebieski\ICore\Casts\ProgressCast::class,
        'translated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'progress' => 100,
        'translated_at' => null
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
                'source' => 'name'
            ]
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \N1ebieski\ICore\Database\Factories\CategoryLang\CategoryLangFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\CategoryLang\CategoryLangFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\Category\Category::class);
    }

    // Factories

    /**
     *
     * @return CategoryLangService
     */
    public function makeService()
    {
        return App::make(CategoryLangService::class, ['categoryLang' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CategoryLangFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }

    /**
     * [makeResource description]
     * @return CategoryLangResource [description]
     */
    public function makeResource()
    {
        return App::make(CategoryLangResource::class, ['categoryLang' => $this]);
    }
}
