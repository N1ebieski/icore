<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\Link\LinkCache;
use N1ebieski\ICore\ValueObjects\Link\Type;
use N1ebieski\ICore\Services\Link\LinkService;
use N1ebieski\ICore\Repositories\Link\LinkRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\Models\Traits\HasPositionable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Database\Factories\Link\LinkFactory;

/**
 * @property Type $type
 */
class Link extends Model
{
    use HasPositionable;
    use HasCarbonable;
    use HasFilterable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'url',
        'name',
        'img_url',
        'home',
        'position'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'type' => \N1ebieski\ICore\Casts\Link\TypeCast::class,
        'home' => 'boolean',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Undocumented variable
     *
     * @var string
     */
    public $path = 'vendor/icore/links';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\Link\LinkFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function siblings(): HasMany
    {
        return $this->hasMany(\N1ebieski\ICore\Models\Link::class, 'type', 'type');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\ICore\Models\Category\Category::class,
            'model',
            'categories_models',
            'model_id',
            'category_id'
        );
    }

    // Scopes

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterType(Builder $query, string $type = null): ?Builder
    {
        return $query->when($type !== null, function ($query) use ($type) {
            $query->where('type', $type);
        });
    }

    // Accessors

    /**
     * [getImgUrlFromStorageAttribute description]
     * @return string|null [description]
     */
    public function getImgUrlFromStorageAttribute(): ?string
    {
        return $this->img_url !== null ? url('/') . Storage::url($this->img_url) : null;
    }

    /**
     * [getBacklinkAsHtmlAttribute description]
     * @return string [description]
     */
    public function getLinkAsHtmlAttribute(): string
    {
        $output = '<a href="' . e($this->url) . '" title="' . e($this->name) . '">';

        if ($this->img_url !== null) {
            $output .= '<img src="' . e($this->img_url_from_storage) . '" alt="' . e($this->name) . '" class="img-fluid">';
        } else {
            $output .= e($this->name);
        }

        $output .= '</a>';

        return $output;
    }

    // Loads

    /**
     * [loadAncestorsWithoutSelf description]
     * @return self [description]
     */
    public function loadAncestorsWithoutSelf(): self
    {
        return $this->load(['categories' => function ($query) {
            $query->withAncestorsExceptSelf();
        }]);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return LinkRepo [description]
     */
    public function makeRepo()
    {
        return App::make(LinkRepo::class, ['link' => $this]);
    }

    /**
     * [makeCache description]
     * @return LinkCache [description]
     */
    public function makeCache()
    {
        return App::make(LinkCache::class, ['link' => $this]);
    }

    /**
     * [makeService description]
     * @return LinkService [description]
     */
    public function makeService()
    {
        return App::make(LinkService::class, ['link' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return LinkFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
