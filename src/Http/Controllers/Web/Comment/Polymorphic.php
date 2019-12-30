<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Requests\Web\Comment\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\TakeRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\EditRequest;
use Illuminate\Http\JsonResponse;

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
     * Gets the next few Comments-childrens without pagination
     *
     * @param  Comment        $comment        [description]
     * @param  TakeRequest    $request        [description]
     * @return JsonResponse                   [description]
     */
    public function take(Comment $comment, TakeRequest $request) : JsonResponse;
}
