<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Rating\Comment;

use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Loads\Web\Rating\Comment\RateLoad;
use N1ebieski\ICore\Http\Requests\Web\Rating\Comment\RateRequest;

interface Polymorphic
{
    /**
     *
     * @param Comment $comment
     * @param RateLoad $load
     * @param RateRequest $request
     * @return JsonResponse
     */
    public function rate(Comment $comment, RateLoad $load, RateRequest $request): JsonResponse;
}
