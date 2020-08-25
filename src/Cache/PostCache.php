<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

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
     * Undocumented variable
     *
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
     * @param Post $post
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Post $post, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->post = $post;
        
        $this->cache = $cache;
        $this->carbon = $carbon;

        $this->minutes = $config->get('cache.minutes');
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->post->makeRepo()->paginateLatest();
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->post->makeRepo()->firstPrevious();
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->post->makeRepo()->firstNext();
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->post->makeRepo()->getRelated();
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->post->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * I have to disable cache in tags because it takes too much memory in Redis
     */

    // /**
    //  * [rememberByTag description]
    //  * @param  Tag                  $tag  [description]
    //  * @param  int                  $page [description]
    //  * @return LengthAwarePaginator       [description]
    //  */
    // public function rememberByTag(Tag $tag, int $page) : LengthAwarePaginator
    // {
    //     return cache()->tags(['posts'])->remember(
    //         "post.paginateByTag.{$tag->normalized}.".$page,
    //         $this->carbon->now()->addMinutes($this->minutes),
    //         function () use ($tag) {
    //             return $this->post->makeRepo()->paginateByTag($tag->name);
    //         }
    //     );
    // }

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
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($month, $year) {
                return $this->post->makeRepo()->paginateArchiveByDate([
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                $posts = $this->post->makeRepo()->getArchives();

                $posts->map(function ($item) {
                    $item->month_localized = $this->carbon->createFromFormat('m', $item->month)
                    ->formatLocalized('%B');
                });

                return $posts;
            }
        );
    }
}
