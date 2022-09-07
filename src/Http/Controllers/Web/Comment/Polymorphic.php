<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Controllers\Web\Comment;

use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Filters\Web\Comment\TakeFilter;
use N1ebieski\ICore\Http\Requests\Web\Comment\EditRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\TakeRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\UpdateRequest;

interface Polymorphic
{
    /**
     * Show the form for editing the specified Comment.
     *
     * @param  Comment      $comment [description]
     * @param  EditRequest  $request [description]
     * @return JsonResponse          [description]
     */
    public function edit(Comment $comment, EditRequest $request): JsonResponse;

    /**
     * Update the specified Comment in storage.
     *
     * @param  Comment        $comment        [description]
     * @param  UpdateRequest  $request        [description]
     * @return JsonResponse                   [description]
     */
    public function update(Comment $comment, UpdateRequest $request): JsonResponse;

    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param TakeRequest $request
     * @param TakeFilter $filter
     * @return JsonResponse
     */
    public function take(Comment $comment, TakeRequest $request, TakeFilter $filter): JsonResponse;
}
