<?php

namespace N1ebieski\ICore\Cache;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Cache\Repository as Cache;

class SessionCache
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
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Configuration
     * @var int
     */
    protected $minutes = 5;

    /**
     * Undocumented function
     *
     * @param DB $db
     * @param Cache $cache
     * @param Carbon $carbon
     */
    public function __construct(DB $db, Cache $cache, Carbon $carbon)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->carbon = $carbon;
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberCountByType(): Collection
    {
        return $this->cache->tags(['sessions'])->remember(
            "session.countByType",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->db->table('sessions')
                    ->selectRaw("IF(`user_id` IS NULL, 'guest', 'user') AS `type`, COUNT(`id`) AS `count`")
                    ->where(
                        'last_activity',
                        '>=',
                        $this->carbon->now()->subMinutes($this->minutes)->timestamp
                    )
                    ->groupBy('user_id')
                    ->get();
            }
        );
    }
}
