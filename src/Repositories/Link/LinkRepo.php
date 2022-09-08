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

namespace N1ebieski\ICore\Repositories\Link;

use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\ICore\ValueObjects\Link\Type;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LinkRepo
{
    /**
     * Undocumented function
     *
     * @param Link $link
     * @param Config $config
     * @param MigrationUtil $migrationUtil
     */
    public function __construct(
        protected Link $link,
        protected Config $config,
        protected MigrationUtil $migrationUtil
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
        return $this->link->where('type', $filter['type'])
            ->filterExcept($filter['except'])
            ->orderBy('position', 'asc')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [getAvailableBacklinksByCatsId description]
     * @param  array      $ids [description]
     * @return Collection      [description]
     */
    public function getAvailableBacklinksByCats(array $ids): Collection
    {
        return $this->link->where('type', 'backlink')
            ->where(function ($query) use ($ids) {
                $query->whereDoesntHave('categories')
                    ->orWhereHas('categories', function ($query) use ($ids) {
                        $query->whereIn('id', array_values($ids));
                    });
            })->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray(): array
    {
        return $this->link->siblings()->pluck('position', 'id')->toArray();
    }

    /**
     * [getLinksByComponent description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function getLinksByComponent(array $component): Collection
    {
        return $this->link->where('type', Type::LINK)
            ->when($component['home'] === true, function ($query) {
                return $query->whereDoesntHave('categories')
                    ->when($this->migrationUtil->contains('add_home_to_links_table'), function ($query) {
                        return $query->orWhere('home', true);
                    });
            }, function ($query) {
                return $query->where(function ($query) {
                    return $query->whereDoesntHave('categories')
                        ->when($this->migrationUtil->contains('add_home_to_links_table'), function ($query) {
                            return $query->where('home', false);
                        });
                });
            })
            ->when($component['cats'] !== null, function ($query) use ($component) {
                return $query->orWhereHas('categories', function ($query) use ($component) {
                    return $query->whereIn('id', $component['cats']);
                });
            })
            ->orderBy('position', 'asc')
            ->limit($component['limit'])
            ->get(['id', 'url', 'name', 'img_url']);
    }
}
