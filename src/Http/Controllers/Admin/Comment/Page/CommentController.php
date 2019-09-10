<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Page;

use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Comment\Page\Comment as PageComment;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use Illuminate\View\View;
use N1ebieski\ICore\Events\CommentStore;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController as CommentBaseController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Polymorphic;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Page\Polymorphic as PagePolymorphic;

/**
 * [CommentController description]
 */
class CommentController extends CommentBaseController implements Polymorphic, PagePolymorphic
{
    /**
     * [__construct description]
     * @param PageComment        $comment        [description]]
     */
    public function __construct(PageComment $comment)
    {
        parent::__construct($comment);
    }

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
        return parent::index($this->comment, $request, $filter);
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
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Page $page, StoreRequest $request) : JsonResponse
    {
        $comment = $this->comment->setMorph($page)->getService()
            ->create($request->only(['content', 'parent_id']));

        event(new CommentStore($comment));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.comment', [
                    'comment' => $comment
                ])->render()
        ]);
    }
}
