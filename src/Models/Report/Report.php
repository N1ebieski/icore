<?php

namespace N1ebieski\ICore\Models\Report;

use N1ebieski\ICore\Services\ReportService;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use N1ebieski\ICore\Traits\Polymorphic;

/**
 * [Report description]
 */
class Report extends Model
{
    use Polymorphic;

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
        return $this->morphTo('model');
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

    // Getters

    /**
     * [getService description]
     * @return ReportService [description]
     */
    public function getService() : ReportService
    {
        return app()->make(ReportService::class, ['report' => $this]);
    }
}
