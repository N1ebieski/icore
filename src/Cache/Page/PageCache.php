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

namespace N1ebieski\ICore\Cache\Page;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class PageCache
{
    /**
     * Undocumented function
     *
     * @param Page $page
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected Page $page,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * [rememberWithChildrensByComponent description]
     * @param  array      $component [description]
     * @return Collect            [description]
     */
    public function rememberWithChildrensByComponent(array $component): Collect
    {
        $json = json_encode($component);

        return $this->cache->tags(['pages'])->remember(
            "page.{$this->config->get('app.locale')}.getWithChildrensByComponent.{$json}",
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
            "page.{$this->config->get('app.locale')}.getWithRecursiveChildrensByComponent.{$json}",
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
            "page.{$this->config->get('app.locale')}.firstBySlug.{$slug}",
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
            "page.{$this->config->get('app.locale')}.loadSiblingsAndRecursiveChildrens.{$this->page->slug}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                return $this->page->loadRecursiveChildrens()->loadActiveSiblings();
            }
        );
    }
}
