<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Page;

use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use Illuminate\View\View;
use N1ebieski\ICore\Events\Admin\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController as CommentBaseController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Page\Polymorphic;

/**
 * [CommentController description]
 */
class CommentController extends CommentBaseController implements Polymorphic
{
    /**
     * Display a listing of Comments.
     *
     * @param  Comment       $comment       [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return View                         [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : View
    {
        $comments = $comment->makeRepo()->paginateByFilter($filter->all() + [
            'except' => $request->input('except')
        ]);

        return view('icore::admin.comment.index', [
            'model' => $comment,
            'comments' => $comments,
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Comment for Page.
     *
     * @param Page $page
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Page $page, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.create', [
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
        $comment = $comment->setMorph($page)->makeService()
            ->create($request->only(['content', 'parent_id']));

        event(new CommentStoreEvent($comment));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.partials.comment', [
                    'comment' => $comment
                ])->render()
        ]);
    }
}
