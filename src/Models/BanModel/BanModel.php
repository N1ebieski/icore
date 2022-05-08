<?php

namespace N1ebieski\ICore\Models\BanModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;

class BanModel extends Model
{
    use Filterable;
    use HasFullTextSearchable;
    use HasPolymorphic;
    use Carbonable;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphTo
     */
    public function morph(): MorphTo
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
    public function scopeFilterOrderBy(Builder $query, $orderby = null): Builder
    {
        $order = explode('|', $orderby);

        if (count($order) == 2) {
            return $query->orderBy($order[0], $order[1])->orderBy('bans_models.id', 'asc');
        }

        return $query->latest('bans_models.created_at');
    }
}
