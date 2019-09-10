<?php

namespace N1ebieski\ICore\Models;

use Spatie\Permission\Models\Permission as BasePermission;
use N1ebieski\ICore\Repositories\PermissionRepo;

/**
 * [Permission description]
 */
class Permission extends BasePermission
{
    // Getters

    /**
     * [getRepo description]
     * @return PermissionRepo [description]
     */
    public function getRepo() : PermissionRepo
    {
        return app()->make(PermissionRepo::class, ['permission' => $this]);
    }
}
