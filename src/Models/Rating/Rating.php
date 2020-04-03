<?php

namespace N1ebieski\ICore\Models\Rating;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Services\RatingService;
use N1ebieski\ICore\Models\Traits\Polymorphic;

/**
 * [Rating description]
 */
class Rating extends Model
{
    use Polymorphic;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rating',
        'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'model_id' => 'integer',
        'rating' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    // Relations

    /**
     * [user description]
     * @return BelongsTo [description]
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [morph description]
     * @return MorphTo [description]
     */
    public function morph() : MorphTo
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    // Makers

    /**
     * [makeService description]
     * @return RatingService [description]
     */
    public function makeService()
    {
        return App::make(RatingService::class, ['rating' => $this]);
    }
}
