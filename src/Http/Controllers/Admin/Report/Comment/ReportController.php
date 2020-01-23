<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Report\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Controllers\Admin\Report\Comment\Polymorphic;

/**
 * [ReportController description]
 */
class ReportController implements Polymorphic
{
    /**
     * Display all the specified Reports for Comment.
     *
     * @param  Comment  $comment [description]
     * @return JsonResponse          [description]
     */
    public function show(Comment $comment) : JsonResponse
    {
        $reports = $comment->reports()->with('user:id,name')->get();

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.report.show', [
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
    public function clear(Comment $comment) : JsonResponse
    {
        $comment->reports()->delete();

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.partials.comment', [
                'comment' => $comment->load('morph:id,title')
            ])->render()
        ]);
    }
}
