<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Report\Comment;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Controllers\Admin\Report\Comment\Polymorphic;

class ReportController implements Polymorphic
{
    /**
     * Display all the specified Reports for Comment.
     *
     * @param  Comment  $comment [description]
     * @return JsonResponse          [description]
     */
    public function show(Comment $comment): JsonResponse
    {
        $reports = $comment->reports()->with('user:id,name')->get();

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.report.show', [
                'reports' => $reports,
                'model' => $comment
            ])->render()
        ]);
    }

    /**
     * Clear all Reports for specified Comment.
     *
     * @param  Comment $comment [description]
     * @return JsonResponse         [description]
     */
    public function clear(Comment $comment): JsonResponse
    {
        $comment->reports()->delete();

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.comment.partials.comment', [
                'comment' => $comment->load('morph:id,title')
            ])->render()
        ]);
    }
}
