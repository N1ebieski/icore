<?php

namespace N1ebieski\ICore\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Link;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [LinkRepo description]
 */
class LinkRepo
{
    /**
     * [protected description]
     * @var Link
     */
    protected $link;

    /**
     * Config
     * @var int
     */
    protected $paginate;

    /**
     * [__construct description]
     * @param Link   $link   [description]
     * @param Config $config [description]
     */
    public function __construct(Link $link, Config $config)
    {
        $this->link = $link;
        $this->paginate = $config->get('database.paginate');
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->link->where('type', $filter['type'])
            ->filterExcept($filter['except'])
            ->orderBy('position', 'asc')
            ->paginate($this->paginate);
    }

    /**
     * [getAvailableBacklinksByCatsId description]
     * @param  array      $ids [description]
     * @return Collection      [description]
     */
    public function getAvailableBacklinksByCats(array $ids) : Collection
    {
        return $this->link->where('type', 'backlink')
            ->where(function($query) use ($ids) {
                $query->whereDoesntHave('categories')
                    ->orWhereHas('categories', function($query) use ($ids) {
                        $query->whereIn('id', array_values($ids));
                    });
            })->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray() : array
    {
        return $this->link->siblings()
            ->get(['id', 'position'])
            ->pluck('position', 'id')
            ->toArray();
    }

    /**
     * [getLinksByComponent description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function getLinksByComponent(array $component) : Collection
    {
        return $this->link->where('type', 'link')
            ->whereDoesntHave('categories')
            ->when($component['cats'] !== null, function($query) use ($component) {
                $query->orWhereHas('categories', function ($query) use ($component) {
                    $query->whereIn('id', $component['cats']);
                });
            })
            ->orderBy('position', 'asc')
            ->limit($component['limit'])
            ->get(['id', 'url', 'name', 'img_url']);
    }
}
