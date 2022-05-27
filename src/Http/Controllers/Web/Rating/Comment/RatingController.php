<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Rating\Comment;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Loads\Web\Rating\Comment\RateLoad;
use N1ebieski\ICore\Http\Requests\Web\Rating\Comment\RateRequest;
use N1ebieski\ICore\Http\Controllers\Web\Rating\Comment\Polymorphic;

class RatingController implements Polymorphic
{
    /**
     *
     * @param Comment $comment
     * @param RateLoad $load
     * @param RateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function rate(Comment $comment, RateLoad $load, RateRequest $request): JsonResponse
    {
        $load->getRating()->makeService()->createOrUpdateOrDelete($request->only('rating'));

        return Response::json([
            'sum_rating' => (int)$comment->ratings->sum('rating')
        ]);
    }
}
