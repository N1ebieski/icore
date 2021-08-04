<?php

namespace N1ebieski\ICore\Cache;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\User;
use Illuminate\Contracts\Auth\Factory as Auth;
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
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Cache $cache
     * @param Config $config
     * @param Collect $collect
     * @param Carbon $carbon
     * @param Auth $auth
     */
    public function __construct(
        User $user,
        Cache $cache,
        Config $config,
        Collect $collect,
        Carbon $carbon,
        Auth $auth
    ) {
        $this->user = $user;

        $this->cache = $cache;
        $this->collect = $collect;
        $this->carbon = $carbon;
        $this->auth = $auth;

        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * Undocumented function
     *
     * @param array $filter
     * @param integer $page
     * @return LengthAwarePaginator
     */
    public function rememberByFilter(array $filter, int $page) : LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems() && !$this->auth->check()) {
            $users = $this->getByFilter($page);
        }

        if (!isset($users) || !$users) {
            $users = $this->user->makeRepo()->paginateByFilter($filter);

            if ($this->collect->make($filter)->isNullItems() && !$this->auth->check()) {
                $this->putByFilter($users, $page);
            }
        }

        return $users;
    }

    /**
     * [getByFilter description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getByFilter(int $page) : ?LengthAwarePaginator
    {
        return $this->cache->tags(["users"])
            ->get(
                "user.getByFilter.{$page}"
            );
    }

    /**
     * [putByFilter description]
     * @param  LengthAwarePaginator $users [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putByFilter(LengthAwarePaginator $users, int $page) : bool
    {
        return $this->cache->tags(["users"])
        ->put(
            "user.getByFilter.{$page}",
            $users,
            $this->carbon->now()->addMinutes($this->minutes)
        );
    }
}
