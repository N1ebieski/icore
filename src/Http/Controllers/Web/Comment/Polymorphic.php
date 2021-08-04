<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Requests\Web\Comment\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\TakeRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\EditRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Filters\Web\Comment\TakeFilter;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Show the form for editing the specified Comment.
     *
     * @param  Comment      $comment [description]
     * @param  EditRequest  $request [description]
     * @return JsonResponse          [description]
     */
    public function edit(Comment $comment, EditRequest $request) : JsonResponse;

    /**
     * Update the specified Comment in storage.
     *
     * @param  Comment        $comment        [description]
     * @param  UpdateRequest  $request        [description]
     * @return JsonResponse                   [description]
     */
    public function update(Comment $comment, UpdateRequest $request) : JsonResponse;

    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param TakeRequest $request
     * @param TakeFilter $filter
     * @return JsonResponse
     */
    public function take(Comment $comment, TakeRequest $request, TakeFilter $filter) : JsonResponse;
}
