<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Http\Requests\Admin\Role\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Role\StoreRequest;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\Permission;
use Illuminate\View\View;

/**
 * [RoleController description]
 */
class RoleController
{
    /**
     *  Display a listing of the Role.
     *
     * @param  Role $role [description]
     * @param IndexRequest $request [description]
     * @return View       [description]
     */
    public function index(Role $role, IndexRequest $request) : View
    {
        return view('icore::admin.role.index', [
            'roles' => $role->getRepo()->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource
     *
     * @param  Permission $permission [description]
     * @return View                 [description]
     */
    public function create(Permission $permission) : View
    {
        $permissions = $permission->orderBy('name', 'asc')->get();

        return view('icore::admin.role.create', [
            'permissions' => $permissions,
            'col_count' => (int)round($permissions->count() / 3, 0)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Role         $role    [description]
     * @param  StoreRequest $request [description]
     * @return RedirectResponse                [description]
     */
    public function store(Role $role, StoreRequest $request) : RedirectResponse
    {
        $role->getService()->create($request->only(['name', 'perm']));

        return redirect()->route('admin.role.index')
            ->with('success', trans('icore::roles.success.store') );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Role       $role       [description]
     * @return View                   [description]
     */
    public function edit(Role $role) : View
    {
        $permissions = $role->getService()->getPermissionsByRole();

        return view('icore::admin.role.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'col_count' => (int)round($permissions->count() / 3, 0)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Role    $role    [description]
     * @param  UpdateRequest $request [description]
     * @return RedirectResponse           [description]
     */
    public function update(Role $role, UpdateRequest $request) : RedirectResponse
    {
        $role->getService()->update($request->only(['perm', 'name']));

        return redirect()->route('admin.role.edit', [$role->id])
            ->with('success', trans('icore::roles.success.update') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role   $role [description]
     * @return RedirectResponse       [description]
     */
    public function destroy(Role $role) : RedirectResponse
    {
        $role->delete();

        return redirect()->route('admin.role.index')->with('success', trans('icore::roles.success.destroy'));
    }
}
