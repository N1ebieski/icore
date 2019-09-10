<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Page;

use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\StoreRequest;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
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
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Page $page, StoreRequest $request) : JsonResponse;
}
