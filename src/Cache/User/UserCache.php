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

namespace N1ebieski\ICore\Cache\User;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserCache
{
    /**
     * Undocumented function
     *
     * @param User $user
     * @param Cache $cache
     * @param Config $config
     * @param Collect $collect
     * @param Carbon $carbon
     * @param Request $request
     */
    public function __construct(
        protected User $user,
        protected Cache $cache,
        protected Config $config,
        protected Collect $collect,
        protected Carbon $carbon,
        protected Request $request
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function rememberByFilter(array $filter): LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems() && !$this->request->user()) {
            $users = $this->getByFilter();
        }

        if (!isset($users)) {
            $users = $this->user->makeRepo()->paginateByFilter($filter);

            if ($this->collect->make($filter)->isNullItems() && !$this->request->user()) {
                $this->putByFilter($users);
            }
        }

        return $users;
    }

    /**
     * [getByFilter description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getByFilter(): ?LengthAwarePaginator
    {
        return $this->cache->tags(["users"])
            ->get(
                "user.getByFilter.{$this->request->input('page')}"
            );
    }

    /**
     * [putByFilter description]
     * @param  LengthAwarePaginator $users [description]
     * @return bool                           [description]
     */
    public function putByFilter(LengthAwarePaginator $users): bool
    {
        return $this->cache->tags(["users"])
        ->put(
            "user.getByFilter.{$this->request->input('page')}",
            $users,
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes'))
        );
    }
}
