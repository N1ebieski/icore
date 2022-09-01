<?php

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
     * Model
     * @var User
     */
    protected $user;

    /**
     * Cache driver
     * @var Cache
     */
    protected $cache;

    /**
     * [protected description]
     * @var Config
     */
    protected $config;

    /**
     * Config
     * @var int
     */
    protected $minutes;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [protected description]
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

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
        User $user,
        Cache $cache,
        Config $config,
        Collect $collect,
        Carbon $carbon,
        Request $request
    ) {
        $this->user = $user;

        $this->cache = $cache;
        $this->config = $config;
        $this->collect = $collect;
        $this->carbon = $carbon;
        $this->request = $request;
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
