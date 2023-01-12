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

namespace N1ebieski\ICore\Cache\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryCache
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     * @param Collect $collect
     * @param Request $request
     */
    public function __construct(
        protected Category $category,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon,
        protected Collect $collect,
        protected Request $request
    ) {
        //
    }

    /**
     * Cache route binding of Category
     * @param  string $slug [description]
     * @return Category|null       [description]
     */
    public function rememberBySlug(string $slug)
    {
        return $this->cache->remember(
            "category.{$this->config->get('app.locale')}.firstBySlug.{$slug}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($slug) {
                return $this->category->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberPosts description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberPosts(): LengthAwarePaginator
    {
        return $this->cache->tags(['posts'])->remember(
            "category.{$this->category->id}.paginatePosts.{$this->request->input('page')}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                return $this->category->makeRepo()->paginatePosts();
            }
        );
    }

    /**
     * [rememberWithRecursiveChildrens description]
     * @return Collection [description]
     */
    public function rememberWithRecursiveChildrens(): Collection
    {
        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.{$this->config->get('app.locale')}.getWithRecursiveChildrens",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                return $this->category->makeRepo()->getWithRecursiveChildrens();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberCountByStatus(): Collection
    {
        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.countByStatus",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                return $this->category->makeRepo()->countByStatus();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param array<string, string> $filter
     * @return LengthAwarePaginator
     */
    public function rememberByFilter(array $filter): LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems() && !$this->request->user()) {
            $categories = $this->getByFilter();
        }

        if (!isset($categories)) {
            $categories = $this->category->makeRepo()->paginateByFilter($filter);

            if ($this->collect->make($filter)->isNullItems() && !$this->request->user()) {
                $this->putByFilter($categories);
            }
        }

        return $categories;
    }

    /**
     * [getByFilter description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getByFilter(): ?LengthAwarePaginator
    {
        return $this->cache->tags(["categories"])
            ->get(
                "category.{$this->category->poli}.{$this->config->get('app.locale')}.paginateByFilter.{$this->request->input('page')}"
            );
    }

    /**
     * [putByFilter description]
     * @param  LengthAwarePaginator $categories [description]
     * @return bool                           [description]
     */
    public function putByFilter(LengthAwarePaginator $categories): bool
    {
        return $this->cache->tags(["categories"])
            ->put(
                "category.{$this->category->poli}.{$this->config->get('app.locale')}.paginateByFilter.{$this->request->input('page')}",
                $categories,
                $this->carbon->now()->addMinutes($this->config->get('cache.minutes'))
            );
    }
}
