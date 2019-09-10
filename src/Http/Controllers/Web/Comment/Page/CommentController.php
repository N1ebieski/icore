<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Page;

use N1ebieski\ICore\Http\Requests\Web\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Page\StoreRequest;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Events\CommentStore;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Page\Polymorphic as PagePolymorphic;

/**
 * [CommentController description]
 */
class CommentController implements PagePolymorphic
{
    /**
     * Show the form for creating a new Comment for Page.
     *
     * @param  Page          $page    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Page $page, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::web.comment.create', [
                'model' => $page,
                'parent_id' => $request->get('parent_id')
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Page         $page    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Page $page, Comment $comment, StoreRequest $request) : JsonResponse
    {
        $comment = $comment->setMorph($page)->getService()->create($request->only(['content', 'parent_id']));

        event(new CommentStore($comment));

        return response()->json([
            'success' => $comment->status === 1 ? '' : trans('icore::comments.success.store_0'),
            'view' => $comment->status === 1 ?
                view('icore::web.comment.comment', [
                    'comment' => $comment
                ])->render() : null
        ]);
    }
}
