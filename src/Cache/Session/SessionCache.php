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

namespace N1ebieski\ICore\Cache\Session;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Cache\Repository as Cache;

class SessionCache
{
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
    public function __construct(
        protected DB $db,
        protected Cache $cache,
        protected Carbon $carbon
    ) {
        //
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
