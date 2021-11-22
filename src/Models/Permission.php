<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Repositories\PermissionRepo;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    // Configuration

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Factories

    /**
     * [makeRepo description]
     * @return PermissionRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PermissionRepo::class, ['permission' => $this]);
    }
}
