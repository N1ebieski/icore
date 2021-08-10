<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Page;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Page\Polymorphic;
use N1ebieski\ICore\Events\Admin\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController as BaseCommentController;

class CommentController implements Polymorphic
{
    /**
     * Undocumented variable
     *
     * @var BaseCommentController
     */
    protected $decorated;

    /**
     * Undocumented function
     *
     * @param BaseCommentController $decorated
     */
    public function __construct(BaseCommentController $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Display a listing of Comments.
     *
     * @param  Comment       $comment       [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                         [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return $this->decorated->index($comment, $request, $filter);
    }

    /**
     * Show the form for creating a new Comment for Page.
     *
     * @param Page $page
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Page $page, CreateRequest $request): JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.comment.create', [
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
    public function store(Page $page, Comment $comment, StoreRequest $request): JsonResponse
    {
        $comment = $comment->setRelations(['morph' => $page])
            ->makeService()
            ->create($request->only(['content', 'parent_id']));

        Event::dispatch(App::make(CommentStoreEvent::class, ['comment' => $comment]));

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.comment.partials.comment', [
                'comment' => $comment
            ])->render()
        ]);
    }
}
