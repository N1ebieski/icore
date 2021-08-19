<?php

namespace N1ebieski\ICore\Cache;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Auth\Factory as Auth;
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
     * @var Auth
     */
    protected $auth;

    /**
     * Config
     * @var int
     */
    protected $minutes;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     * @param Collect $collect
     * @param Auth $auth
     */
    public function __construct(
        Category $category,
        Cache $cache,
        Config $config,
        Carbon $carbon,
        Collect $collect,
        Auth $auth
    ) {
        $this->category = $category;

        $this->cache = $cache;
        $this->carbon = $carbon;
        $this->collect = $collect;
        $this->auth = $auth;

        $this->minutes = $config->get('cache.minutes');
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->category->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberPosts description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberPosts(int $page): LengthAwarePaginator
    {
        return $this->cache->tags(['posts'])->remember(
            "category.{$this->category->id}.paginatePosts.{$page}",
            $this->carbon->now()->addMinutes($this->minutes),
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
            $this->carbon->now()->addMinutes($this->minutes),
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->category->makeRepo()->countByStatus();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param array $filter
     * @param integer $page
     * @return LengthAwarePaginator
     */
    public function rememberByFilter(array $filter, int $page): LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems() && $this->auth->guest()) {
            $categories = $this->getByFilter($page);
        }

        if (!isset($categories) || !$categories) {
            $categories = $this->category->makeRepo()->paginateByFilter($filter);

            if ($this->collect->make($filter)->isNullItems() && $this->auth->guest()) {
                $this->putByFilter($categories, $page);
            }
        }

        return $categories;
    }

    /**
     * [getByFilter description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getByFilter(int $page): ?LengthAwarePaginator
    {
        return $this->cache->tags(["categories"])
            ->get(
                "category.{$this->category->poli}.getByFilter.{$page}"
            );
    }

    /**
     * [putByFilter description]
     * @param  LengthAwarePaginator $categories [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putByFilter(LengthAwarePaginator $categories, int $page): bool
    {
        return $this->cache->tags(["categories"])
            ->put(
                "category.{$this->category->poli}.getByFilter.{$page}",
                $categories,
                $this->carbon->now()->addMinutes($this->minutes)
            );
    }
}
