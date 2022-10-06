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

namespace N1ebieski\ICore\Cache\Link;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Link;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class LinkCache
{
    /**
     * Undocumented function
     *
     * @param Link $link
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected Link $link,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * [rememberLinksByComponent description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function rememberLinksByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['links'])->remember(
            "link.getLinksByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($component) {
                return $this->link->makeRepo()->getLinksByComponent($component);
            }
        );
    }
}
