<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Tag;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [TagCache description]
 */
class TagCache
{
    /**
     * [private description]
     * @var Tag
     */
    protected $tag;

    /**
     * [protected description]
     * @var Cache
     */
    protected $cache;

    /**
     * [protected description]
     * @var int
     */
    protected $minutes;

    /**
     * [__construct description]
     * @param Tag    $tag    [description]
     * @param Cache  $cache  [description]
     * @param Config $config [description]
     */
    public function __construct(Tag $tag, Cache $cache, Config $config)
    {
        $this->tag = $tag;
        $this->cache = $cache;
        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [rememberBySlug description]
     * @param  string $slug [description]
     * @return Tag|null       [description]
     */
    public function rememberBySlug(string $slug) : ?Tag
    {
        return $this->cache->remember(
            "tag.firstBySlug.{$slug}",
            now()->addMinutes($this->minutes),
            function() use ($slug) {
                return $this->tag->makeRepo()->firstBySlug($slug);
            }
        );
    }
}
