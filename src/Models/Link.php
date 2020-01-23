<?php

namespace N1ebieski\ICore\Models;

use N1ebieski\ICore\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\LinkService;
use N1ebieski\ICore\Repositories\LinkRepo;
use N1ebieski\ICore\Cache\LinkCache;
use N1ebieski\ICore\Models\Traits\Positionable;
use N1ebieski\ICore\Models\Traits\Carbonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * [Link description]
 */
class Link extends Model
{
    use Positionable, Carbonable, Filterable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'url', 'name', 'img_url', 'position'
    ];

    // Overrides

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function(Link $link) {
            $link->position = $link->position ?? $link->getNextAfterLastPosition();
        });

        // Everytime the model is removed, we have to decrement siblings position by 1
        static::deleted(function(Link $link) {
            $link->decrementSiblings($link->position, null);
        });

        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        static::saved(function(Link $link) {
            $link->reorderSiblings();
        });
    }

    // Relations

    /**
     * [siblings description]
     * @return [type] [description]
     */
    public function siblings()
    {
        return $this->hasMany('N1ebieski\ICore\Models\Link', 'type', 'type');
    }

    /**
     * [categories description]
     * @return [type] [description]
     */
    public function categories()
    {
        return $this->morphToMany('N1ebieski\ICore\Models\Category\Category', 'model', 'categories_models', 'model_id', 'category_id');
    }

    // Scopes

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterType(Builder $query, string $type = null) : ?Builder
    {
        return $query->when($type !== null, function($query) use ($type) {
            $query->where('type', $type);
        });
    }

    // Accessors

    /**
     * [getImgUrlFromStorageAttribute description]
     * @return string|null [description]
     */
    public function getImgUrlFromStorageAttribute() : ?string
    {
        return $this->img_url !== null ? url('/') . Storage::url($this->img_url) : null;
    }

    /**
     * [getBacklinkAsHtmlAttribute description]
     * @return string [description]
     */
    public function getLinkAsHtmlAttribute() : string
    {
        $output = '<a href="' . $this->url . '" title="' . $this->name . '">';

        if ($this->img_url !== null) {
            $output .= '<img src="' . $this->img_url_from_storage . '" alt="' . $this->name . '">';
        } else {
            $output .= $this->name;
        }

        $output .= '</a>';

        return $output;
    }

    // Loads

    /**
     * [loadAncestorsWithoutSelf description]
     * @return self [description]
     */
    public function loadAncestorsWithoutSelf() : self
    {
        return $this->load(['categories' => function($query) {
                $query->withAncestorsExceptSelf();
            }]);
    }

    // Makers

    /**
     * [makeRepo description]
     * @return LinkRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(LinkRepo::class, ['link' => $this]);
    }

    /**
     * [makeCache description]
     * @return LinkCache [description]
     */
    public function makeCache()
    {
        return app()->make(LinkCache::class, ['link' => $this]);
    }

    /**
     * [makeService description]
     * @return LinkService [description]
     */
    public function makeService()
    {
        return app()->make(LinkService::class, ['link' => $this]);
    }
}
