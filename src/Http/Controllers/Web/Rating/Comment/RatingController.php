<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Rating\Comment;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Rating\Comment\Rating;
use N1ebieski\ICore\Http\Requests\Web\Rating\Comment\RateRequest;
use N1ebieski\ICore\Http\Controllers\Web\Rating\Comment\Polymorphic as CommentPolymorphic;

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
    public function rate(Rating $rating, Comment $comment, RateRequest $request): JsonResponse
    {
        $rating->setRelations(['morph' => $comment])
            ->makeService()
            ->createOrUpdateOrDelete($request->only('rating'));

        return Response::json([
            'sum_rating' => (int)$comment->ratings->sum('rating')
        ]);
    }
}
