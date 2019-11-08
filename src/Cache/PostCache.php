<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Tag;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * [PostCache description]
 */
class PostCache
{
    /**
     * Post model
     * @var Post
     */
    protected $post;

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
     * @param Post     $post     [description]
     * @param Cache    $cache    [description]
     * @param Config   $config   [description]
     */
    public function __construct(Post $post, Cache $cache, Config $config)
    {
        $this->post = $post;
        $this->cache = $cache;
        $this->minutes = $config->get('icore.cache.minutes');
    }

    /**
     * [rememberLatest description]
     * @param  int    $page [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberLatest(int $page) : LengthAwarePaginator
    {
        return $this->cache->tags(['posts'])->remember(
            "post.paginateLatest.{$page}",
            now()->addMinutes($this->minutes),
            function () {
                return $this->post->getRepo()->paginateLatest();
            }
        );
    }

    /**
     * [rememberPrevious description]
     * @return Post|null [description]
     */
    public function rememberPrevious()
    {
        return $this->cache->tags(['post.'.$this->post->slug])->remember(
            "post.firstPrevious.{$this->post->id}",
            now()->addMinutes($this->minutes),
            function () {
                return $this->post->getRepo()->firstPrevious();
            }
        );
    }

    /**
     * [rememberNext description]
     * @return Post|null [description]
     */
    public function rememberNext()
    {
        return $this->cache->tags(['post.'.$this->post->slug])->remember(
            "post.firstNext.{$this->post->id}",
            now()->addMinutes($this->minutes),
            function () {
                return $this->post->getRepo()->firstNext();
            }
        );
    }

    /**
     * [rememberRelated description]
     * @return Collection [description]
     */
    public function rememberRelated() : Collection
    {
        return $this->cache->tags(['post.'.$this->post->slug])->remember(
            "post.getRelated.{$this->post->id}",
            now()->addMinutes($this->minutes),
            function () {
                return $this->post->getRepo()->getRelated();
            }
        );
    }

    /**
     * Cache route binding
     * @param  string $slug [description]
     * @return Post|null       [description]
     */
    public function rememberBySlug(string $slug)
    {
        return $this->cache->tags(['post.'.$slug])->remember(
            "post.firstBySlug.{$slug}",
            now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->post->getRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberByTag description]
     * @param  Tag                  $tag  [description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberByTag(Tag $tag, int $page) : LengthAwarePaginator
    {
        return cache()->tags(['posts'])->remember(
            "post.paginateByTag.{$tag->normalized}.".$page,
            now()->addMinutes($this->minutes),
            function() use ($tag) {
                return $this->post->getRepo()->paginateByTag($tag->name);
            }
        );
    }

    /**
     * [rememeberArchiveByDate description]
     * @param  int                  $month [description]
     * @param  int                  $year  [description]
     * @param  int                  $page  [description]
     * @return LengthAwarePaginator        [description]
     */
    public function rememeberArchiveByDate(int $month, int $year, int $page) : LengthAwarePaginator
    {
        return $this->cache->tags(['posts'])->remember(
            "post.paginateArchiveByDate.{$month}.{$year}.{$page}",
            now()->addMinutes($this->minutes),
            function() use ($month, $year) {
                return $this->post->getRepo()->paginateArchiveByDate([
                    'month' => $month,
                    'year' => $year
                ]);
            }
        );
    }

    /**
     * [rememberArchives description]
     * @return Collection [description]
     */
    public function rememberArchives() : Collection
    {
        return $this->cache->tags(['posts'])->remember(
            'post.getArchives',
            now()->addMinutes($this->minutes),
            function() {
                $posts = $this->post->getRepo()->getArchives();

                $posts->map(function($item) {
                    $item->month_localized = Carbon::createFromFormat('m', $item->month)
                    ->formatLocalized('%B');
                });

                return $posts;
            }
        );
    }

    /**
     * [rememberPopularTags description]
     * @return array [description]
     */
    public function rememberPopularTags() : array
    {
        return $this->cache->remember(
            'post.popularTags',
            now()->addMinutes($this->minutes),
            function() {
                return $this->post->popularTags(25);
            }
        );
    }
}
