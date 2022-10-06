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

namespace N1ebieski\ICore\Cache\Stat;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class StatCache
{
    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected Stat $stat,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
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
