<?php

namespace N1ebieski\ICore\Models\Report\Comment;

use N1ebieski\ICore\Models\Report\Report as ReportBaseModel;
use N1ebieski\ICore\Models\Comment\Comment;

/**
 * [Comment description]
 */
class Report extends ReportBaseModel
{
    /**
     * [protected description]
     * @var Report
     */
    protected $morph;

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'comment';
    }

    // Setters

    /**
     * [setMorph description]
     * @param Comment $comment [description]
     * @return $this
     */
    public function setMorph(Comment $comment)
    {
        $this->morph = $comment;

        return $this;
    }

    // Makers

    /**
     * [getMorph description]
     * @return Comment [description]
     */
    public function getMorph()
    {
        return $this->morph;
    }
}
