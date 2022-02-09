<?php

namespace N1ebieski\ICore\Cache;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class PageCache
{
    /**
     * Post model
     * @var Page
     */
    protected $page;

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
     * [protected description]
     * @var Carbon
     */
    protected $carbon;

    /**
     * Configuration
     * @var int
     */
    protected $minutes;

    /**
     * Undocumented function
     *
     * @param Page $page
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Page $page, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->page = $page;

        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
    }

    /**
     * [rememberWithChildrensByComponent description]
     * @param  array     $component [description]
     * @return Collection            [description]
     */
    public function rememberWithChildrensByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['pages'])->remember(
            "page.getWithChildrensByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($component) {
                return $this->page->makeRepo()->getWithChildrensByComponent($component);
            }
        );
    }

    /**
     * [rememberWithChildrensByComponent description]
     * @param  array     $component [description]
     * @return Collection            [description]
     */
    public function rememberWithRecursiveChildrensByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['pages'])->remember(
            "page.getWithRecursiveChildrensByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($component) {
                return $this->page->makeRepo()->getWithRecursiveChildrensByComponent($component);
            }
        );
    }

    /**
     * Cache route binding
     * @param  string $slug [description]
     * @return Page|null       [description]
     */
    public function rememberBySlug(string $slug): ?Page
    {
        return $this->cache->tags(['page.' . $slug])->remember(
            "page.firstBySlug.{$slug}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($slug) {
                return $this->page->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberLoadSiblingsAndRecursiveChildrens description]
     * @return Page [description]
     */
    public function rememberLoadSiblingsAndRecursiveChildrens(): Page
    {
        return $this->cache->tags(['page.' . $this->page->slug])->remember(
            "page.loadSiblingsAndRecursiveChildrens.{$this->page->slug}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                return $this->page->loadRecursiveChildrens()->loadActiveSiblings();
            }
        );
    }
}
