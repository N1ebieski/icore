<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Requests\Web\Comment\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\TakeRequest;
use Illuminate\Http\JsonResponse;

/**
 * [CommentController description]
 */
class CommentController
{
    /**
     * Show the form for editing the specified Comment.
     *
     * @param  Comment      $comment [description]
     * @return JsonResponse          [description]
     */
    public function edit(Comment $comment) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::web.comment.edit', [
                'comment' => $comment
            ])->render()
        ]);
    }

    /**
     * Update the specified Comment in storage.
     *
     * @param  Comment        $comment        [description]
     * @param  UpdateRequest  $request        [description]
     * @return JsonResponse                   [description]
     */
    public function update(Comment $comment, UpdateRequest $request) : JsonResponse
    {
        $comment->getService()->update($request->only('content'));

        return response()->json([
            'success' => '',
            'view' => view('icore::web.comment.comment', [
                'comment' => $comment,
                'post_id' => $comment->model_id
            ])->render()
        ]);
    }

    /**
     * Gets the next few Comments-childrens without pagination
     *
     * @param  Comment        $comment        [description]
     * @param  TakeRequest    $request        [description]
     * @return JsonResponse                   [description]
     */
    public function take(Comment $comment, TakeRequest $request) : JsonResponse
    {
        $comments = $comment->getService()->paginateChildrensByFilter([
            'except' => $request->get('except'),
            'orderby' => $request->get('orderby')
        ]);

        return response()->json([
            'success' => '',
            'view' => view('icore::web.comment.take', [
                'comments' => $comments,
                'parent' => $comment
            ])->render()
        ]);
    }
}
