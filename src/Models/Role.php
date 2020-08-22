<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Traits\Filterable;
use Spatie\Permission\Models\Role as BaseRole;
use N1ebieski\ICore\Repositories\RoleRepo;
use N1ebieski\ICore\Services\RoleService;
use N1ebieski\ICore\Models\Traits\Carbonable;

/**
 * [Role description]
 */
class Role extends BaseRole
{
    use Carbonable, Filterable;

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
    public function makeRepo()
    {
        return App::make(RoleRepo::class, ['role' => $this]);
    }

    /**
     * [makeService description]
     * @return RoleService [description]
     */
    public function makeService()
    {
        return App::make(RoleService::class, ['role' => $this]);
    }
}
