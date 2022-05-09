<?php

namespace N1ebieski\ICore\Cache\Stat;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class StatCache
{
    /**
     * Model
     * @var Stat
     */
    protected $stat;

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
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Stat $stat, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->stat = $stat;

        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
    }

    /**
     * Cache route binding of Stat
     * @param  string $slug [description]
     * @return Stat|null       [description]
     */
    public function rememberBySlug(string $slug)
    {
        return $this->cache->remember(
            "stat.{$this->stat->poli}.firstBySlug.{$slug}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($slug) {
                return $this->stat->makeRepo()->firstBySlug($slug);
            }
        );
    }
}
