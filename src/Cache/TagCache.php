<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

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
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * [protected description]
     * @var int
     */
    protected $minutes;

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Tag $tag, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->tag = $tag;

        $this->cache = $cache;
        $this->carbon = $carbon;

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
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->tag->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberPopularByComponent description]
     * @param  array $component [description]
     * @return Collection [description]
     */
    public function rememberPopularByComponent(array $component) : Collection
    {
        $cats = $component['cats'] !== null ? implode('.', $component['cats']) : null;

        return $this->cache->remember(
            "tag.{$this->tag->poli}.getPopularByComponent.{$cats}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->tag->makeRepo()->getPopularByComponent($component);
            }
        );
    }
    
    /**
     * Undocumented function
     *
     * @param Collection $tags
     * @param array $component
     * @return bool
     */
    public function putPopularByComponent(Collection $tags, array $component) : bool
    {
        $cats = $component['cats'] !== null ? implode('.', $component['cats']) : null;

        return $this->cache->put(
            "tag.{$this->tag->poli}.getPopularByComponent.{$cats}",
            $tags,
            $this->carbon->now()->addMinutes($this->minutes)
        );
    }
}
