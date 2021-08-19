<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\DestroyGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateCensoredRequest;

interface Polymorphic
{
    /**
     * Display a listing of Comments.
     *
     * @param  Comment       $comment       [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                         [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : HttpResponse;

    /**
     * Display the specified Comment.
     *
     * @param  Comment  $comment [description]
     * @return JsonResponse          [description]
     */
    public function show(Comment $comment) : JsonResponse;

    /**
     * Show the form for editing the specified Comment.
     *
     * @param  Comment $comment
     * @return JsonResponse
     */
    public function edit(Comment $comment) : JsonResponse;

    /**
     * Update the specified Comment in storage.
     *
     * @param  Comment       $comment [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function update(Comment $comment, UpdateRequest $request) : JsonResponse;

    /**
     * Update Censored attribute the specified Comment in storage.
     *
     * @param  Comment               $comment [description]
     * @param  UpdateCensoredRequest $request [description]
     * @return JsonResponse                      [description]
     */
    public function updateCensored(Comment $comment, UpdateCensoredRequest $request) : JsonResponse;

    /**
     * Update Status attribute the specified Comment in storage.
     *
     * @param  Comment             $comment [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                     [description]
     */
    public function updateStatus(Comment $comment, UpdateStatusRequest $request) : JsonResponse;

    /**
     * Remove the specified Comment from storage.
     *
     * @param  Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment) : JsonResponse;

    /**
     * Remove the collection of Comments from storage.
     *
     * @param  Comment              $comment [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Comment $comment, DestroyGlobalRequest $request) : RedirectResponse;
}
