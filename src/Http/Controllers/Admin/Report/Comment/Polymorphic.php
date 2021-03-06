<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Report\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Http\JsonResponse;

interface Polymorphic
{
    /**
     * Display all the specified Reports for Comment.
     *
     * @param  Comment  $comment [description]
     * @return JsonResponse          [description]
     */
    public function show(Comment $comment) : JsonResponse;

    /**
     * Clear all Reports for specified Comment.
     *
     * @param  Comment $comment [description]
     * @return JsonResponse         [description]
     */
    public function clear(Comment $comment) : JsonResponse;
}
