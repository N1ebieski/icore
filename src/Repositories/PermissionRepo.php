<?php

namespace N1ebieski\ICore\Repositories;

use InvalidArgumentException;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
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
