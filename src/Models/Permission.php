<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
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
        return App::make(PermissionRepo::class, ['permission' => $this]);
    }
}
