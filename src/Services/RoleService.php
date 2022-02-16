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
        $this->setRole($role);

        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param Role $role
     * @return static
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * [getPermissionsByRole description]
     * @return Collection [description]
     */
    public function getPermissionsByRole(): Collection
    {
        switch ($this->role->name) {
            case 'user':
                return $this->role->permissions()->make()->makeRepo()
                    ->getUserWithRole($this->role->id);

            case 'api':
                return $this->role->permissions()->make()->makeRepo()
                    ->getApiWithRole($this->role->id);

            default:
                return $this->role->permissions()->make()->makeRepo()
                    ->getWithRole($this->role->id);
        }
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $role = $this->role->create(['name' => $attributes['name']]);

            if (array_key_exists('perm', $attributes)) {
                $role->givePermissionTo(array_filter($attributes['perm']) ?? []);
            }

            return $role;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('perm', $attributes)) {
                $this->role->syncPermissions(array_filter($attributes['perm']) ?? []);
            }

            return $this->role->update(['name' => $attributes['name']]);
        });
    }
}
