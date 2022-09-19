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

namespace N1ebieski\ICore\Services\Role;

use Throwable;
use N1ebieski\ICore\Models\Role;
use Illuminate\Database\DatabaseManager as DB;

class RoleService
{
    /**
     * Undocumented function
     *
     * @param Role $role
     * @param DB $db
     */
    public function __construct(
        protected Role $role,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Role
     * @throws Throwable
     */
    public function create(array $attributes): Role
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
     *
     * @param array $attributes
     * @return Role
     * @throws Throwable
     */
    public function update(array $attributes): Role
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('perm', $attributes)) {
                $this->role->syncPermissions(array_filter($attributes['perm']) ?? []);
            }

            $this->role->update(['name' => $attributes['name']]);

            return $this->role;
        });
    }
}
