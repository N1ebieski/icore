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

namespace N1ebieski\ICore\Filters\Traits;

use N1ebieski\ICore\Models\Role;

trait HasRole
{
    /**
     * @param Role $role
     * @return self
     */
    public function setRole(Role $role): self
    {
        $this->parameters['role'] = $role;

        return $this;
    }

    /**
     *
     * @param int|null $id
     * @return void
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
     * @param int|null $id
     * @return null|Role
     */
    public function findRole(int $id = null): ?Role
    {
        return Role::find($id);
    }
}
