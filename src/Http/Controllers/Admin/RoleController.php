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
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Models\Permission;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\Role\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Role\EditRequest;
use N1ebieski\ICore\Http\Requests\Admin\Role\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Role\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Role\DestroyRequest;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class RoleController
{
    /**
     *  Display a listing of the Role.
     *
     * @param  Role        $role    [description]
     * @param IndexRequest $request [description]
     * @return HttpResponse         [description]
     */
    public function index(Role $role, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('icore::admin.role.index', [
            'roles' => $role->makeRepo()->paginateByFilter($filter->all()),
        ]);
    }

    /**
     * Show the form for creating a new resource
     *
     * @param  Permission $permission [description]
     * @return HttpResponse           [description]
     */
    public function create(Permission $permission): HttpResponse
    {
        $permissions = $permission->orderBy('name', 'asc')->get();

        return Response::view('icore::admin.role.create', [
            'permissions' => $permissions,
            'col_count' => (int)ceil($permissions->count() / 3)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Role         $role    [description]
     * @param  StoreRequest $request [description]
     * @return RedirectResponse                [description]
     */
    public function store(Role $role, StoreRequest $request): RedirectResponse
    {
        $role->makeService()->create($request->validated());

        return Response::redirectToRoute('admin.role.index')
            ->with('success', Lang::get('icore::roles.success.store'));
    }

    /**
     *
     * @param Role $role
     * @param Permission $permission
     * @param EditRequest $request
     * @return HttpResponse
     */
    public function edit(Role $role, Permission $permission, EditRequest $request): HttpResponse
    {
        $permissions = $permission->makeRepo()->getByRole($role);

        return Response::view('icore::admin.role.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'col_count' => (int)ceil($permissions->count() / 3)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Role    $role    [description]
     * @param  UpdateRequest $request [description]
     * @return RedirectResponse           [description]
     */
    public function update(Role $role, UpdateRequest $request): RedirectResponse
    {
        $role->makeService()->update($request->validated());

        return Response::redirectToRoute('admin.role.edit', [$role->id])
            ->with('success', Lang::get('icore::roles.success.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role           $role     [description]
     * @param  DestroyRequest $request  [description]
     * @return RedirectResponse         [description]
     */
    public function destroy(Role $role, DestroyRequest $request): RedirectResponse
    {
        $role->delete();

        return Response::redirectToRoute('admin.role.index')
            ->with('success', Lang::get('icore::roles.success.destroy'));
    }
}
