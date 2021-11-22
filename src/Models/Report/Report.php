<?php

namespace N1ebieski\ICore\Models\Report;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\ReportService;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use Polymorphic;
    use Carbonable;

    // Configuration

    /**
     * [public description]
     * @var int
     */
    public const REPORTED = 1;

    /**
     * [public description]
     * @var int
     */
    public const UNREPORTED = 0;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'model_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations

    /**
     * [user description]
     * @return BelongsTo [description]
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\User::class);
    }

    /**
     * [morph description]
     * @return MorphTo [description]
     */
    public function morph(): MorphTo
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    // Factories

    /**
     * [makeService description]
     * @return ReportService [description]
     */
    public function makeService()
    {
        return App::make(ReportService::class, ['report' => $this]);
    }
}
