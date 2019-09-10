<?php

namespace N1ebieski\ICore\Models;

use Spatie\Permission\Models\Role as BaseRole;
use N1ebieski\ICore\Repositories\RoleRepo;
use N1ebieski\ICore\Services\RoleService;
use Carbon\Carbon;

/**
 * [Role description]
 */
class Role extends BaseRole
{
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

    // Accessors

    /**
     * [getCreatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getCreatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * [getUpdatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getUpdatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }

    // Getters

    /**
     * [getRepo description]
     * @return RoleRepo [description]
     */
    public function getRepo() : RoleRepo
    {
        return app()->make(RoleRepo::class, ['role' => $this]);
    }

    /**
     * [getService description]
     * @return RoleService [description]
     */
    public function getService() : RoleService
    {
        return app()->make(RoleService::class, ['role' => $this]);
    }
}
