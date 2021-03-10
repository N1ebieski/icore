<?php

namespace N1ebieski\ICore\Models\Report\Comment;

use N1ebieski\ICore\Models\Report\Report as ReportBaseModel;

/**
 * [Comment description]
 */
class Report extends ReportBaseModel
{
    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'comment';
    }
}
