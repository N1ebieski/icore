<?php

namespace N1ebieski\ICore\Models\BanModel;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * [BanModel description]
 */
class BanModel extends Model
{
    use Filterable, FullTextSearchable, Polymorphic;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_type', 'model_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bans_models';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'model_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    // Relations

    /**
     * [morph description]
     * @return [type] [description]
     */
    public function morph()
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    // Scopes

    /**
     * [scopeFilterOrderBy description]
     * @param  Builder $query   [description]
     * @param  [type]  $orderby [description]
     * @return Builder           [description]
     */
    public function scopeFilterOrderBy(Builder $query, $orderby = null) : Builder
    {
        $order = explode('|', $orderby);

        if (count($order) == 2) {
            return $query->orderBy($order[0] ?: 'updated_at', $order[1] ?: 'desc')
                ->orderBy('bans_models.id', 'asc');
        }

        return $query->latest('bans_models.created_at');
    }

    // Accessors

    /**
     * [getCreatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getCreatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * [getUpdatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getUpdatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }
}
