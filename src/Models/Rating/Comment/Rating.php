<?php

namespace N1ebieski\ICore\Models\Rating\Comment;

use N1ebieski\ICore\Models\Rating\Rating as RatingBaseModel;
use N1ebieski\ICore\Models\Comment\Comment;

/**
 * [Comment description]
 */
class Rating extends RatingBaseModel
{
    /**
     * [protected description]
     * @var Rating
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

    // Getters

    /**
     * [getMorph description]
     * @return Comment [description]
     */
    public function getMorph()
    {
        return $this->morph;
    }
}
