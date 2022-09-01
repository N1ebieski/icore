<?php

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
     * Model
     * @var Category
     */
    protected $category;

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
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $collect;

    /**
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

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
        Category $category,
        Cache $cache,
        Config $config,
        Carbon $carbon,
        Collect $collect,
        Request $request
    ) {
        $this->category = $category;

        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
        $this->collect = $collect;
        $this->request = $request;
    }

    /**
     * Cache route binding of Category
     * @param  string $slug [description]
     * @return Category|null       [description]
     */
    public function rememberBySlug(string $slug)
    {
        return $this->cache->remember(
            "category.firstBySlug.{$slug}",
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
            "category.{$this->category->poli}.getWithRecursiveChildrens",
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
                "category.{$this->category->poli}.paginateByFilter.{$this->request->input('page')}"
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
                "category.{$this->category->poli}.paginateByFilter.{$this->request->input('page')}",
                $categories,
                $this->carbon->now()->addMinutes($this->config->get('cache.minutes'))
            );
    }
}
