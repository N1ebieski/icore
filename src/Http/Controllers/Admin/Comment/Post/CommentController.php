<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Comment\Post\Comment as PostComment;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use Illuminate\View\View;
use N1ebieski\ICore\Events\CommentStore;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController as CommentBaseController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Polymorphic;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Post\Polymorphic as PostPolymorphic;

/**
 * [CommentController description]
 */
class CommentController extends CommentBaseController implements Polymorphic, PostPolymorphic
{
    /**
     * [__construct description]
     * @param PostComment        $comment        [description]
     */
    public function __construct(PostComment $comment)
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
     * Show the form for creating a new Comment for Post.
     *
     * @param Post $post
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Post $post, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.create', [
                'model' => $post,
                'parent_id' => $request->get('parent_id')
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Post         $post    [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Post $post, StoreRequest $request) : JsonResponse
    {
        $comment = $this->comment->setMorph($post)->getService()
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
