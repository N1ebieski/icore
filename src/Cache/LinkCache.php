<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Link;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Collection;

/**
 * [LinkCache description]
 */
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
     * Configuration
     * @var int
     */
    protected $minutes;

    /**
     * [__construct description]
     * @param Link   $link   [description]
     * @param Cache  $cache  [description]
     * @param Config $config [description]
     */
    public function __construct(Link $link, Cache $cache, Config $config)
    {
        $this->link = $link;
        $this->cache = $cache;
        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [rememberLinksByComponent description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function rememberLinksByComponent(array $component) : Collection
    {
        $cats = $component['cats'] !== null ? implode('.', $component['cats']) : null;

        return $this->cache->tags(['links'])->remember(
            "link.getLinksByComponent.{$cats}",
            now()->addMinutes($this->minutes),
            function() use ($component) {
                return $this->link->makeRepo()->getLinksByComponent($component);
            }
        );
    }
}
