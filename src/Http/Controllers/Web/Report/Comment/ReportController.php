<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Report\Comment;

use N1ebieski\ICore\Http\Requests\Web\Report\Comment\CreateRequest;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Report\Comment\Report;
use N1ebieski\ICore\Http\Requests\Web\Report\Comment\StoreRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Controllers\Web\Report\Comment\Polymorphic as CommentPolymorphic;

/**
 * [ReportController description]
 */
class ReportController implements CommentPolymorphic
{
    /**
     * Display all the specified Reports for Comment.
     *
     * @param  Comment  $comment [description]
     * @param CreateRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function create(Comment $comment, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::web.report.create', [
                'model' => $comment
            ])->render()
        ]);
    }

    /**
     * Store a newly created Report for Comment in storage.
     *
     * @param  Comment       $comment       [description]
     * @param  Report        $report        [description]
     * @param  StoreRequest  $request       [description]
     * @return JsonResponse                 [description]
     */
    public function store(Comment $comment, Report $report, StoreRequest $request) : JsonResponse
    {
        $report->setMorph($comment)->makeService()->create($request->only('content'));

        return response()->json([
            'success' => trans('icore::reports.success.store')
        ]);
    }
}
