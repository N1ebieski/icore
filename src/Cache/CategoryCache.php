<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * [CategoryCache description]
 */
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
     */
    public function __construct(Category $category, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->category = $category;

        $this->cache = $cache;
        $this->carbon = $carbon;

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
    public function rememberPosts(int $page) : LengthAwarePaginator
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
    public function rememberWithRecursiveChildrens() : Collection
    {
        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.getWithRecursiveChildrens",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->category->makeRepo()->getWithRecursiveChildrens();
            }
        );
    }
}
