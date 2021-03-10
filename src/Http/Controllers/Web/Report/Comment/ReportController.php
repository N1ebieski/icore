<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Report\Comment;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Report\Comment\Report;
use N1ebieski\ICore\Http\Requests\Web\Report\Comment\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Report\Comment\CreateRequest;
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
        return Response::json([
            'success' => '',
            'view' => View::make('icore::web.report.create', [
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
        $report->setRelations(['morph' => $comment])
            ->makeService()
            ->create($request->only('content'));

        return Response::json([
            'success' => Lang::get('icore::reports.success.store')
        ]);
    }
}
