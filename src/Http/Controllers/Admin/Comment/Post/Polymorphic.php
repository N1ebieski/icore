<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Post\StoreRequest;
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
     * @param Post $post
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Post $post, CreateRequest $request) : JsonResponse;

    /**
     * [store description]
     * @param  Post         $post    [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Post $post, StoreRequest $request) : JsonResponse;
}
