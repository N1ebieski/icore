<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Services\Serviceable;

/**
 * [RoleService description]
 */
class RoleService implements Serviceable
{
    /**
     * Model
     * @var Role
     */
    protected $role;

    /**
     * [private description]
     * @var Permission
     */
    protected $permission;

    /**
     * [__construct description]
     * @param Role $role     [description]
     * @param Permission $permission    [description]
     */
    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }

    /**
     * [getPermissionsByRole description]
     * @return Collection [description]
     */
    public function getPermissionsByRole() : Collection
    {
        if ($this->role->name === 'user') {
            return $this->permission->getRepo()->getUserWithRole($this->role->id);
        }

        return $this->permission->getRepo()->getWithRole($this->role->id);
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $role = $this->role->create(['name' => $attributes['name']]);

        $role->givePermissionTo(array_filter($attributes['perm']) ?? []);

        return $role;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->role->syncPermissions(array_filter($attributes['perm']) ?? []);

        return $this->role->update(['name' => $attributes['name']]);
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {

    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {

    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {

    }
}
