<?php

namespace N1ebieski\ICore\Policies;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * [RolePolicy description]
 */
class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * [actionDefault description]
     * @param  User   $current_user [description]
     * @param  Role   $role         [description]
     * @return bool               [description]
     */
    public function editDefault(User $current_user, Role $role) : bool
    {
        return !in_array($role->name, ['super-admin', 'admin']);
    }

    /**
     * [deleteDefault description]
     * @param  User $current_user [description]
     * @param  Role $role         [description]
     * @return bool               [description]
     */
    public function deleteDefault(User $current_user, Role $role) : bool
    {
        return !in_array($role->name, ['super-admin', 'admin', 'user']);
    }
}
