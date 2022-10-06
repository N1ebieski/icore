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

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\User\IndexFilter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use N1ebieski\ICore\Http\Requests\Admin\User\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\DestroyGlobalRequest;

class UserController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the Users.
     *
     * @param  User          $user          [description]
     * @param  Role          $role          [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                         [description]
     */
    public function index(User $user, Role $role, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('icore::admin.user.index', [
            'users' => $user->makeRepo()->paginateByFilter($filter->all()),
            'roles' => $role->all(),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Update Status attribute the specified User in storage.
     *
     * @param  User                $user    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(User $user, UpdateStatusRequest $request): JsonResponse
    {
        $user->makeService()->updateStatus($request->input('status'));

        return Response::json([
            'status' => $user->status->getValue(),
            'view' => View::make('icore::admin.user.partials.user', [
                'user' => $user
            ])->render(),
        ]);
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  User         $user [description]
     * @return JsonResponse       [description]
     */
    public function destroy(User $user): JsonResponse
    {
        $user->makeService()->delete();

        return Response::json(['success' => '']);
    }

    /**
     * Remove the collection of Users from storage.
     *
     * @param  User                 $user    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(User $user, DestroyGlobalRequest $request): RedirectResponse
    {
        $this->authorize('deleteGlobalSelf', [User::class, $request->input('select')]);

        $deleted = $user->makeService()->deleteGlobal($request->get('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::users.success.destroy_global', ['affected' => $deleted])
        );
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  User         $user [description]
     * @param  Role         $role [description]
     * @return JsonResponse       [description]
     */
    public function edit(User $user, Role $role): JsonResponse
    {
        $roles = $role->makeRepo()->getAvailable();

        return Response::json([
            'view' => View::make('icore::admin.user.edit', compact('user', 'roles'))->render()
        ]);
    }

    /**
     * Update the specified User in storage.
     *
     * @param  User          $user    [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function update(User $user, UpdateRequest $request): JsonResponse
    {
        $user->makeService()->update($request->validated());

        return Response::json([
            'view' => View::make('icore::admin.user.partials.user', [
                'user' => $user
            ])->render(),
        ]);
    }

    /**
     * Show the form for creating a new User.
     *
     * @param  Role         $role [description]
     * @return JsonResponse       [description]
     */
    public function create(Role $role): JsonResponse
    {
        $roles = $role->makeRepo()->getAvailable();

        return Response::json([
            'view' => View::make('icore::admin.user.create', [
                'roles' => $roles
            ])->render()
        ]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  User         $user    [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(User $user, StoreRequest $request): JsonResponse
    {
        $user = $user->makeService()->create($request->validated());

        $request->session()->flash('success', Lang::get('icore::users.success.store'));

        return Response::json([
            'redirect' => URL::route("admin.user.index", [
                'filter' => [
                    'search' => "id:\"{$user->id}\""
                ]
            ])
        ]);
    }
}
