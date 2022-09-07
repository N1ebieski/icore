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

namespace N1ebieski\ICore\Http\Controllers\Web\Rating\Comment;

use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Loads\Web\Rating\Comment\RateLoad;
use N1ebieski\ICore\Http\Requests\Web\Rating\Comment\RateRequest;

interface Polymorphic
{
    /**
     *
     * @param Comment $comment
     * @param RateLoad $load
     * @param RateRequest $request
     * @return JsonResponse
     */
    public function rate(Comment $comment, RateLoad $load, RateRequest $request): JsonResponse;
}
