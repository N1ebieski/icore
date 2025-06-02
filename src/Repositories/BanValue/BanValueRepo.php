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

namespace N1ebieski\ICore\Repositories\BanValue;

use N1ebieski\ICore\Models\BanValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BanValueRepo
{
    /**
     *
     * @param BanValue $banValue
     * @param Auth $auth
     * @return void
     */
    public function __construct(
        protected BanValue $banValue,
        protected Auth $auth
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
        return $this->banValue->newQuery()
            ->selectRaw("`{$this->banValue->getTable()}`.*")
            ->filterType($filter['type'])
            ->filterExcept($filter['except'])
            ->when(!is_null($filter['search']), function (Builder|BanValue $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.bans.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach (['id'] as $attr) {
                                $query = $query->when(array_key_exists($attr, $this->banValue->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->banValue->getTable()}.{$attr}", $this->banValue->search[$attr]);
                                });
                            }

                            return $query;
                        });
                    });
            })
            ->when($filter['orderby'] === null, function (Builder|BanValue $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }
}
