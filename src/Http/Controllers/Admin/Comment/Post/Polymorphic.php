<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\CreateRequest;

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
     * @return HttpResponse                 [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : HttpResponse;

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
