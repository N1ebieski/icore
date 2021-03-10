<?php

namespace N1ebieski\ICore\Models\Rating\Comment;

use N1ebieski\ICore\Models\Rating\Rating as RatingBaseModel;

/**
 * [Comment description]
 */
class Rating extends RatingBaseModel
{
    // Configuration

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
