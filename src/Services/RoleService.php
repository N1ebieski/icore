<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;

class RoleService implements Creatable, Updatable
{
    /**
     * Model
     * @var Role
     */
    protected $role;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Role $role
     * @param DB $db
     */
    public function __construct(Role $role, DB $db)
    {
        $this->role = $role;

        $this->db = $db;
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
        return $this->db->transaction(function () use ($attributes) {
            $role = $this->role->create(['name' => $attributes['name']]);

            $role->givePermissionTo(array_filter($attributes['perm']) ?? []);

            return $role;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->role->syncPermissions(array_filter($attributes['perm']) ?? []);

            return $this->role->update(['name' => $attributes['name']]);
        });
    }
}
