<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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
     * Config
     * @var int
     */
    protected $minutes;

    /**
     * [__construct description]
     * @param Category     $category     [description]
     * @param Cache        $cache        [description]
     * @param Config       $config       [description]
     */
    public function __construct(Category $category, Cache $cache, Config $config)
    {
        $this->category = $category;
        $this->cache = $cache;
        $this->minutes = $config->get('icore.cache.minutes');
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
            now()->addMinutes($this->minutes),
            function() use ($slug) {
                return $this->category->getRepo()->firstBySlug($slug);
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
            "category.getPosts.{$this->category->id}.{$page}",
            now()->addMinutes($this->minutes),
            function () {
                return $this->category->getRepo()->paginatePosts();
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
            'category.getWithRecursiveChildrens',
            now()->addMinutes($this->minutes),
            function() {
                return $this->category->getRepo()->getWithRecursiveChildrens();
            }
        );
    }
}
