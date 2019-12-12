<?php

namespace N1ebieski\ICore\Models;

use Spatie\Permission\Models\Role as BaseRole;
use N1ebieski\ICore\Repositories\RoleRepo;
use N1ebieski\ICore\Services\RoleService;
use N1ebieski\ICore\Models\Traits\Carbonable;
use Carbon\Carbon;

/**
 * [Role description]
 */
class Role extends BaseRole
{
    use Carbonable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name'
    ];

    // Checkers

    /**
     * [isEditDefault description]
     * @return bool [description]
     */
    public function isEditNotDefault() : bool
    {
        return !in_array($this->name, ['super-admin', 'admin']);
    }

    /**
     * [isDeleteDefault description]
     * @return bool [description]
     */
    public function isDeleteNotDefault() : bool
    {
        return !in_array($this->name, ['super-admin', 'admin', 'user']);
    }

    // Makers

    /**
     * [makeRepo description]
     * @return RoleRepo [description]
     */
    public function makeRepo() : RoleRepo
    {
        return app()->make(RoleRepo::class, ['role' => $this]);
    }

    /**
     * [makeService description]
     * @return RoleService [description]
     */
    public function makeService() : RoleService
    {
        return app()->make(RoleService::class, ['role' => $this]);
    }
}
