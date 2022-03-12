<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Http\Resources\User\UserResource;

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
     * @responseField id int
     * @responseField name string
     * @responseField ip string (available only for admin.users.view).
     * @responseField email string (available only for admin.users.view or owner).
     * @responseField status object Contains int value and string label
     * @responseField marketing object Email marketing consent, contains int value and string label (available only for admin.users.view or owner).
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField roles object[] Contains relationships Roles.
     * @responseField socialites object[] Contains relationships Socialites (available only for admin.users.view or owner).
     *
     * @apiResource N1ebieski\ICore\Http\Resources\User\UserResource
     * @apiResourceModel N1ebieski\ICore\Models\User states=active,user with=roles
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        return App::make(UserResource::class, ['user' => Auth::user()])->response();
    }
}
