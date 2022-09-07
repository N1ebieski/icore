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

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Http\Requests\Web\Comment\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Post\CreateRequest;

interface Polymorphic
{
    /**
     * Show the form for creating a new Comment for Post.
     *
     * @param  Post          $post    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Post $post, CreateRequest $request): JsonResponse;

    /**
     * [store description]
     * @param  Post         $post    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Post $post, Comment $comment, StoreRequest $request): JsonResponse;
}
