<?php

namespace N1ebieski\ICore\Http\Controllers\Api\User;

use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Filters\Api\User\IndexFilter;
use N1ebieski\ICore\Http\Resources\Role\RoleResource;
use N1ebieski\ICore\Http\Resources\User\UserResource;
use N1ebieski\ICore\Http\Requests\Api\User\IndexRequest;

/**
 * @group Users
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/users.php
 *
 * > Controller:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\User\UserController
 *
 * > Resource:
 *
 *     N1ebieski\ICore\Http\Resources\User\UserResource
 *
 * Permissions:
 *
 * - api.* - access to all api endpoints
 * - api.users.* - access to all users endpoints
 * - api.users.view - access to endpoints with collection of users
 */
class UserController
{
    /**
     * Index of users
     *
     * <aside class="notice">Available only to users with permissions: api.users.view and admin.users.view.</aside>
     *
     * @authenticated
     *
     * @bodyParam filter.status int Must be one of 1 or 0 (available only for admin.users.view). Example: 1
     *
     * @responseField id int
     * @responseField name string
     * @responseField ip string (available only for admin.users.view).
     * @responseField email string (available only for admin.users.view or owner).
     * @responseField status object Contains int value and string label
     * @responseField marketing object Email marketing consent, contains int value and string label (available only for admin.users.view or owner).
     * @responseField created_at string
     * @responseField created_at_diff string
     * @responseField updated_at string
     * @responseField updated_at_diff string
     * @responseField roles object[] Contains relationship Roles.
     * @responseField socialites object[] Contains relationship Socialites (available only for admin.users.view or owner).
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\User\UserResource
     * @apiResourceModel N1ebieski\ICore\Models\User states=active,user with=roles
     * @apiResourceAdditional meta="Paging, filtering and sorting information"
     *
     * @param User $user
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(User $user, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return App::make(UserResource::class)
            ->collection(
                $user->makeCache()->rememberByFilter(
                    $filter->all(),
                    $request->input('page') ?? 1
                )
            )
            ->additional(['meta' => [
                'filter' => Collect::make($filter->all())
                    ->replace([
                        'role' => $filter->get('role') instanceof Role ?
                            App::make(RoleResource::class, ['role' => $filter->get('role')])
                            : $filter->get('role')
                    ])
                    ->toArray()
            ]])
            ->response();
    }
}
