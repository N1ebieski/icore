<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Post;

use N1ebieski\ICore\Http\Requests\Web\Comment\Post\CreateRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Post\StoreRequest;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * Show the form for creating a new Comment for Post.
     *
     * @param  Post          $post    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
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
