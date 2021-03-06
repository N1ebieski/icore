<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [RoleRepo description]
 */
class RoleRepo
{
    /**
     * [private description]
     * @var Role
     */
    protected $role;

    /**
     * Config
     * @var int
     */
    protected $paginate;

    /**
     * [__construct description]
     * @param Role   $role   [description]
     * @param Config $config [description]
     */
    public function __construct(Role $role, Config $config)
    {
        $this->role = $role;
        
        $this->paginate = $config->get('database.paginate');
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->role->orderBy('id', 'asc')
            ->filterExcept($filter['except'])
            ->paginate($this->paginate);
    }

    /**
     * [allAvailableNamesAsArray description]
     * @return array [description]
     */
    public function getAvailableNamesAsArray() : array
    {
        return $this->getAvailable()
            ->pluck('name')
            ->toArray();
    }

    /**
     * [getAvailable description]
     * @return Collection [description]
     */
    public function getAvailable() : Collection
    {
        return $this->role->where('name', '<>', 'super-admin')->get();
    }

    /**
     * [getIdsAsArray description]
     * @return array [description]
     */
    public function getIdsAsArray() : array
    {
        return $this->role->get('id')->pluck('id')->toArray();
    }
}
