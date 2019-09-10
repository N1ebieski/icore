<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Page;

use N1ebieski\ICore\Http\Requests\Web\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Page\StoreRequest;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Show the form for creating a new Comment for Page.
     *
     * @param  Page          $page    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Page $page, CreateRequest $request) : JsonResponse;

    /**
     * [store description]
     * @param  Page         $page    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Page $page, Comment $comment, StoreRequest $request) : JsonResponse;
}
