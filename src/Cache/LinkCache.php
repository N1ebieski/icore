<?php

namespace N1ebieski\ICore\Cache;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Link;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class LinkCache
{
    /**
     * Link model
     * @var Link
     */
    protected $link;

    /**
     * Cache driver
     * @var Cache
     */
    protected $cache;

    /**
     * Cache driver
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
     * @param Link $link
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Link $link, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->link = $link;

        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
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
