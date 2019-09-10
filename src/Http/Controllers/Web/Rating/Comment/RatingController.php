<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Rating\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Rating\Comment\Rating;
use N1ebieski\ICore\Http\Requests\Web\Rating\Comment\RateRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Controllers\Web\Rating\Comment\Polymorphic as CommentPolymorphic;

/**
 * [RatingController description]
 */
class RatingController implements CommentPolymorphic
{
    /**
     * Add/Update or Delete Rate of specified Comment
     *
     * @param  Rating        $rating
     * @param  Comment       $comment       [description]
     * @param  RateRequest   $request       [description]
     * @return JsonResponse                 [description]
     */
    public function rate(Rating $rating, Comment $comment, RateRequest $request) : JsonResponse
    {
        $rating->setMorph($comment)->getService()->createOrUpdateOrDelete($request->only('rating'));

        return response()->json([
            'success' => '',
            'sum_rating' => (int)$comment->ratings->sum('rating')
        ]);
    }
}
