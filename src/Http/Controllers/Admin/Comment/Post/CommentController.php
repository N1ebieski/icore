<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use Illuminate\View\View;
use N1ebieski\ICore\Events\Admin\Comment\Store as CommentStore;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController as CommentBaseController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Post\Polymorphic;

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
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Post $post, Comment $comment, StoreRequest $request) : JsonResponse
    {
        $comment = $comment->setMorph($post)->makeService()
            ->create($request->only(['content', 'parent_id']));

        event(new CommentStore($comment));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.partials.comment', [
                'comment' => $comment
            ])->render()
        ]);
    }
}
