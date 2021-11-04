<?php

namespace N1ebieski\ICore\Filters\Traits;

use N1ebieski\ICore\Models\Role;

trait HasRole
{
    /**
     * [setRole description]
     * @param Role $role [description]
     */
    public function setRole(Role $role)
    {
        $this->parameters['role'] = $role;

        return $role;
    }

    /**
     * [filterRole description]
     * @param int|null $id [description]
     */
    public function filterRole(int $id = null): void
    {
        $this->parameters['role'] = null;

        if ($id !== null) {
            if ($role = $this->findRole($id)) {
                $this->setRole($role);
            }
        }
    }

    /**
     * [findRole description]
     * @param  int|null $id [description]
     * @return Role       [description]
     */
    public function findRole(int $id = null): Role
    {
        return Role::find($id);
    }
}
