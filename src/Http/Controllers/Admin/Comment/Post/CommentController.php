<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\CreateRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Post\Polymorphic;
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
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : HttpResponse
    {
        return $this->decorated->index($comment, $request, $filter);
    }

    /**
     * Show the form for creating a new Comment for Post.
     *
     * @param Post $post
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Post $post, CreateRequest $request) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.comment.create', [
                'model' => $post,
                'parent_id' => $request->get('parent_id')
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Post         $post    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Post $post, Comment $comment, StoreRequest $request) : JsonResponse
    {
        $comment = $comment->setRelations(['morph' => $post])
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
