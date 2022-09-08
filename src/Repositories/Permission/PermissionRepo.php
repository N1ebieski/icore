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

namespace N1ebieski\ICore\Repositories\Permission;

use InvalidArgumentException;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepo
{
    /**
     * [__construct description]
     * @param Permission $permission [description]
     */
    public function __construct(protected Permission $permission)
    {
        //
    }

    /**
     *
     * @param Role $role
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function getByRole(Role $role): Collection
    {
        return $this->permission->with([
                'roles' => function ($query) use ($role) {
                    $query->where('id', $role->id);
                }
            ])
            ->when($role->name->isUser(), function ($query) {
                $query->where('name', 'like', 'web.%')
                    ->orWhere('name', 'like', 'api.%');
            })
            ->when($role->name->isApi(), function ($query) {
                $query->where('name', 'like', 'api.%');
            })
            ->orderBy('name', 'asc')
            ->get();
    }
}
