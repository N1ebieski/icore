<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Page;

use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use Illuminate\View\View;

/**
 * [interface description]
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
     * Show the form for creating a new Comment for Page.
     *
     * @param Page $page
     * @param CreateRequest $request
     * @return JsonResponse
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
