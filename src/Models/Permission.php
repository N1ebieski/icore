<?php

namespace N1ebieski\ICore\Models;

use Spatie\Permission\Models\Permission as BasePermission;
use N1ebieski\ICore\Repositories\PermissionRepo;

/**
 * [Permission description]
 */
class Permission extends BasePermission
{
    // Makers

    /**
     * [makeRepo description]
     * @return PermissionRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(PermissionRepo::class, ['permission' => $this]);
    }
}
