<?php

namespace N1ebieski\ICore\Loads\Web\Rating\Comment;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Rating\Comment\Rating;

class RateLoad
{
    /**
     *
     * @var Rating
     */
    protected $rating;

    /**
     *
     * @param Request $request
     * @param Rating $rating
     * @return void
     */
    public function __construct(Request $request, Rating $rating)
    {
        /**
         * @var Comment
         */
        $comment = $request->route('comment');

        $this->rating = $comment->makeRepo()->firstRatingByUser($request->user())
            ?? $rating->setRelations(['morph' => $comment]);
    }

    /**
     * Get the value of rating
     *
     * @return  Rating
     */
    public function getRating(): Rating
    {
        return $this->rating;
    }
}
