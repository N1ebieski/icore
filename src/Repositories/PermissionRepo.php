<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

/**
 * [PermissionRepo description]
 */
class PermissionRepo
{
    /**
     * [private description]
     * @var Permission
     */
    protected $permission;

    /**
     * [__construct description]
     * @param Permission $permission [description]
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * [getUserWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getUserWithRole(int $id) : Collection
    {
        return $this->permission->with(['roles' => function($query) use ($id) {
                $query->where('id', $id);
            }])->whereIn('name', [
                'create comments',
                'suggest comments'
            ])->orderBy('name', 'asc')
            ->get();
    }

    /**
     * [getWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getWithRole(int $id) : Collection
    {
        return $this->permission->with(['roles' => function($query) use ($id) {
                $query->where('id', $id);
            }])->orderBy('name', 'asc')
            ->get();
    }
}
