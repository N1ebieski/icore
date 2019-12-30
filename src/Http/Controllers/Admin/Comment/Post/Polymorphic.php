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

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * Display a listing of Comments.
     *
     * @param  Comment       $comment       [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return View                         [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : View;

    /**
     * Show the form for creating a new Comment for Post.
     *
     * @param Post $post
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Post $post, CreateRequest $request) : JsonResponse;

    /**
     * [store description]
     * @param  Post         $post    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Post $post, Comment $comment, StoreRequest $request) : JsonResponse;
}
