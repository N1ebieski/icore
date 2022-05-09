<?php

namespace N1ebieski\ICore\Cache\Migration;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class MigrationCache
{
    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

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
     * @param DB $db
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(DB $db, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberAll(): Collection
    {
        return $this->cache->tags(['migrations'])->remember(
            "migration.getAll",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                return $this->db->table('migrations')->get();
            }
        );
    }
}
