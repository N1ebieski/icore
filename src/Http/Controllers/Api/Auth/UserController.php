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

namespace N1ebieski\ICore\Http\Controllers\Api\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authenticated user
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/user.php
 *
 * > Controller:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Auth\UserController
 *
 */
class UserController
{
    /**
     * Show user
     *
     * Show authenticated User resource
     *
     * <aside class="notice">Available only to users with permission: api.access.</aside>
     *
     * @authenticated
     *
     * @apiResource N1ebieski\ICore\Http\Resources\User\UserResource
     * @apiResourceModel N1ebieski\ICore\Models\User states=active,user with=roles
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        return Auth::user()->makeResource()->response();
    }
}
