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

namespace N1ebieski\ICore\Cache\Migration;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class MigrationCache
{
    /**
     * Undocumented function
     *
     * @param DB $db
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected DB $db,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
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
