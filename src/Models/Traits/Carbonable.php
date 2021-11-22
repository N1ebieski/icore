<?php

namespace N1ebieski\ICore\Models\Traits;

use Carbon\Carbon;

trait Carbonable
{
    // Accessors

    /**
     * [getCreatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getCreatedAtDiffAttribute(): string
    {
        return Carbon::parse($this->created_at)->diffForHumans(['parts' => 2]);
    }

    /**
     * [getUpdatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getUpdatedAtDiffAttribute(): string
    {
        return Carbon::parse($this->updated_at)->diffForHumans(['parts' => 2]);
    }
}
