<?php

namespace N1ebieski\ICore\Services\Role;

use N1ebieski\ICore\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class RoleService implements CreateInterface, UpdateInterface
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
