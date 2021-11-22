<?php

namespace N1ebieski\ICore\Models\Report\Comment;

use N1ebieski\ICore\Models\Report\Report as BaseReport;

class Report extends BaseReport
{
    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'comment';
    }
}
