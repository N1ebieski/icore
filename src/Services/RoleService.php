<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;

/**
 * [RoleService description]
 */
class RoleService implements Creatable, Updatable
{
    /**
     * Model
     * @var Role
     */
    protected $role;

    /**
     * [__construct description]
     * @param Role $role     [description]
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * [getPermissionsByRole description]
     * @return Collection [description]
     */
    public function getPermissionsByRole() : Collection
    {
        if ($this->role->name === 'user') {
            return $this->role->permissions()->make()->makeRepo()
                ->getUserWithRole($this->role->id);
        }

        return $this->role->permissions()->make()->makeRepo()
            ->getWithRole($this->role->id);
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
}
