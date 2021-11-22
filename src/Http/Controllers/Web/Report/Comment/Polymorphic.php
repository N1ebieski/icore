<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Report\Comment;

use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Report\Comment\Report;
use N1ebieski\ICore\Http\Requests\Web\Report\Comment\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Report\Comment\CreateRequest;

interface Polymorphic
{
    /**
     * Display all the specified Reports for Comment.
     *
     * @param  Comment      $comment [description]
     * @param CreateRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function create(Comment $comment, CreateRequest $request): JsonResponse;

    /**
     * Store a newly created Report for Comment in storage.
     *
     * @param  Comment       $comment       [description]
     * @param  Report        $report        [description]
     * @param  StoreRequest  $request       [description]
     * @return JsonResponse                 [description]
     */
    public function store(Comment $comment, Report $report, StoreRequest $request): JsonResponse;
}
