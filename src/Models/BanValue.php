<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Cache\BanValueCache;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Repositories\BanValueRepo;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;

/**
 * [BanValue description]
 */
class BanValue extends Model
{
    use Filterable, FullTextSearchable, Carbonable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'value'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['value'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bans_values';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterType(Builder $query, string $type = null) : ?Builder
    {
        return $query->when($type !== null, function ($query) use ($type) {
            $query->where('type', $type);
        });
    }

    // Makers

    /**
     * [makeRepo description]
     * @return BanValueRepo [description]
     */
    public function makeRepo()
    {
        return App::make(BanValueRepo::class, ['banvalue' => $this]);
    }

    /**
     * [makeCache description]
     * @return BanValueCache [description]
     */
    public function makeCache()
    {
        return App::make(BanValueCache::class, ['banvalue' => $this]);
    }
}
