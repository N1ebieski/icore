<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Repositories\Role;

use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;

class RoleRepo
{
    /**
     * [__construct description]
     * @param Role   $role   [description]
     * @param Config $config [description]
     */
    public function __construct(
        protected Role $role,
        protected Config $config
    ) {
        //
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->role->orderBy('id', 'asc')
            ->filterExcept($filter['except'])
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [allAvailableNamesAsArray description]
     * @return array [description]
     */
    public function getAvailableNamesAsArray(): array
    {
        return $this->getAvailable()
            ->pluck('name')
            ->toArray();
    }

    /**
     * [getAvailable description]
     * @return Collection [description]
     */
    public function getAvailable(): Collection
    {
        return $this->role->where('name', '<>', Name::SUPER_ADMIN)->get();
    }

    /**
     * [getIdsAsArray description]
     * @return array [description]
     */
    public function getIdsAsArray(): array
    {
        return $this->role->get('id')->pluck('id')->toArray();
    }
}
