<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Rating\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Rating\Comment\Rating;
use N1ebieski\ICore\Http\Requests\Web\Rating\Comment\RateRequest;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * Add/Update or Delete Rate of specified Comment
     *
     * @param  Rating        $rating
     * @param  Comment       $comment       [description]
     * @param  RateRequest   $request       [description]
     * @return JsonResponse                 [description]
     */
    public function rate(Rating $rating, Comment $comment, RateRequest $request) : JsonResponse;
}
