<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Requests\Web\Comment\EditRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\TakeRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\UpdateRequest;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Polymorphic;

/**
 * [CommentController description]
 */
class CommentController implements Polymorphic
{
    /**
     * Show the form for editing the specified Comment.
     *
     * @param  Comment      $comment [description]
     * @param  EditRequest  $request [description]
     * @return JsonResponse          [description]
     */
    public function edit(Comment $comment, EditRequest $request) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::web.comment.edit', [
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
        $comment->makeService()->update($request->only('content'));

        return Response::json([
            'success' => '',
            'view' => View::make('icore::web.comment.partials.comment', [
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
        return Response::json([
            'success' => '',
            'view' => View::make('icore::web.comment.take', [
                'comments' => $comment->makeService()->paginateChildrensByFilter([
                    'except' => $request->get('except'),
                    'orderby' => $request->get('orderby')
                ]),
                'parent' => $comment
            ])->render()
        ]);
    }
}
