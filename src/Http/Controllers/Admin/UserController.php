<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Filters\Admin\User\IndexFilter;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\BanModel\BanModel;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Http\Requests\Admin\User\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\User\DestroyGlobalRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * [UserController description]
 */
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
     * @return View                         [description]
     */
    public function index(User $user, Role $role, IndexRequest $request, IndexFilter $filter) : View
    {
        $users = $user->makeRepo()->paginateByFilter($filter->all() + [
            'except' => $request->input('except')
        ]);

        debug($request->all());

        return view('icore::admin.user.index', [
            'users' => $users,
            'roles' => $role->all(),
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * Update Status attribute the specified User in storage.
     *
     * @param  User                $user    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(User $user, UpdateStatusRequest $request) : JsonResponse
    {
        $user->update($request->only('status'));

        return response()->json([
            'success' => '',
            'status' => $user->status,
            'view' => view('icore::admin.user.partials.user', ['user' => $user])->render(),
        ]);
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  User         $user [description]
     * @return JsonResponse       [description]
     */
    public function destroy(User $user) : JsonResponse
    {
        $user->ban()->delete();

        $user->delete();

        return response()->json(['success' => '']);
    }

    /**
     * Remove the collection of Users from storage.
     *
     * @param  User                 $user    [description]
     * @param  BanModel             $banModel [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(User $user, BanModel $banModel, DestroyGlobalRequest $request) : RedirectResponse
    {
        $this->authorize('deleteGlobalSelf', [User::class, $request->input('select')]);

        $banModel->whereIn('model_id', $request->get('select'))
            ->where('model_type', 'N1ebieski\ICore\Models\User')->delete();

        $deleted = $user->whereIn('id', $request->get('select'))->delete();

        return redirect()->back()->with('success', trans('icore::users.success.destroy_global', ['affected' => $deleted]));
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  User         $user [description]
     * @param  Role         $role [description]
     * @return JsonResponse       [description]
     */
    public function edit(User $user, Role $role) : JsonResponse
    {
        $roles = $role->makeRepo()->getAvailable();

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.user.edit', compact('user', 'roles'))->render()
        ]);
    }

    /**
     * Update the specified User in storage.
     *
     * @param  User          $user    [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function update(User $user, UpdateRequest $request) : JsonResponse
    {
        $user->update($request->only(['name', 'email']));

        $user->syncRoles(array_merge($request->get('roles'), ['user']));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.user.partials.user', ['user' => $user])->render(),
        ]);
    }

    /**
     * Show the form for creating a new User.
     *
     * @param  Role         $role [description]
     * @return JsonResponse       [description]
     */
    public function create(Role $role) : JsonResponse
    {
        $roles = $role->makeRepo()->getAvailable();

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.user.create', ['roles' => $roles])->render()
        ]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  User         $user    [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(User $user, StoreRequest $request) : JsonResponse
    {
        $user = $user->create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);

        $user->assignRole(array_merge($request->get('roles'), ['user']));

        $request->session()->flash('success', trans('icore::users.success.store') );

        return response()->json(['success' => '']);
    }
}
