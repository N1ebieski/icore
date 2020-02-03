<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Collection;

/**
 * [PostCache description]
 */
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
     * Configuration
     * @var int
     */
    protected $minutes;

    /**
     * [__construct description]
     * @param Page   $page   [description]
     * @param Cache  $cache  [description]
     * @param Config $config [description]
     */
    public function __construct(Page $page, Cache $cache, Config $config)
    {
        $this->page = $page;
        $this->cache = $cache;
        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [rememberWithChildrensByComponent description]
     * @param  array     $component [description]
     * @return Collection            [description]
     */
    public function rememberWithChildrensByComponent(array $component) : Collection
    {
        return $this->cache->tags(['pages'])->remember(
            'page.getWithChildrensByComponent',
            now()->addMinutes($this->minutes),
            function() use ($component) {
                return $this->page->makeRepo()->getWithChildrensByComponent($component);
            }
        );
    }

    /**
     * [rememberWithChildrensByComponent description]
     * @param  array     $component [description]
     * @return Collection            [description]
     */
    public function rememberWithRecursiveChildrensByComponent(array $component) : Collection
    {
        return $this->cache->tags(['pages'])->remember(
            'page.getWithRecursiveChildrensByComponent',
            now()->addMinutes($this->minutes),
            function() use ($component) {
                return $this->page->makeRepo()->getWithRecursiveChildrensByComponent($component);
            }
        );
    }

    /**
     * Cache route binding
     * @param  string $slug [description]
     * @return Page|null       [description]
     */
    public function rememberBySlug(string $slug) : ?Page
    {
        return $this->cache->tags(['page.'.$slug])->remember(
            "page.firstBySlug.{$slug}",
            now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->page->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberLoadRecursiveChildrens description]
     * @return Page [description]
     */
    public function rememberLoadRecursiveChildrens() : Page
    {
        return $this->cache->tags(['page.'.$this->page->slug])->remember(
            "page.loadRecursiveChildrens.{$this->page->slug}",
            now()->addMinutes($this->minutes),
            function() {
                return $this->page->loadRecursiveChildrens();
            }
        );
    }
}
