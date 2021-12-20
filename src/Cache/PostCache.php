<?php

namespace N1ebieski\ICore\Cache;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     * @param Collect $collect
     * @param Auth $auth
     */
    public function __construct(
        Post $post,
        Cache $cache,
        Config $config,
        Carbon $carbon,
        Collect $collect,
        Auth $auth
    ) {
        $this->post = $post;

        $this->cache = $cache;
        $this->carbon = $carbon;
        $this->collect = $collect;
        $this->auth = $auth;

        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [rememberLatest description]
     * @param  int    $page [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberLatest(int $page): LengthAwarePaginator
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
        return $this->cache->tags(['post.' . $this->post->slug])->remember(
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
        return $this->cache->tags(['post.' . $this->post->slug])->remember(
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
    public function rememberRelated(): Collection
    {
        return $this->cache->tags(['post.' . $this->post->slug])->remember(
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
        return $this->cache->tags(['post.' . $slug])->remember(
            "post.firstBySlug.{$slug}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->post->makeRepo()->firstBySlug($slug);
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
    public function rememeberArchiveByDate(int $month, int $year, int $page): LengthAwarePaginator
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
    public function rememberArchives(): Collection
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

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberCountByStatus(): Collection
    {
        return $this->cache->tags(['posts'])->remember(
            "post.countByStatus",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->post->makeRepo()->countByStatus();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function rememberLastActivity(): ?string
    {
        return $this->cache->tags(['posts'])->remember(
            "post.lastActivity",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->post->makeRepo()->getLastActivity();
            }
        );
    }

    /**
     * [rememberLatestForHome description]
     * @return Collection [description]
     */
    public function rememberLatestForHome(): Collection
    {
        return $this->cache->tags(["posts"])->remember(
            "post.getLatestForHome",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->post->makeRepo()->getLatestForHome();
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
            $posts = $this->getByFilter($page);
        }

        if (!isset($posts) || !$posts) {
            $posts = $this->post->makeRepo()->paginateByFilter($filter);

            if ($this->collect->make($filter)->isNullItems() && $this->auth->guest()) {
                $this->putByFilter($posts, $page);
            }
        }

        return $posts;
    }

    /**
     * [getByFilter description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getByFilter(int $page): ?LengthAwarePaginator
    {
        return $this->cache->tags(["posts"])
            ->get(
                "post.paginateByFilter.{$page}"
            );
    }

    /**
     * [putByFilter description]
     * @param  LengthAwarePaginator $posts [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putByFilter(LengthAwarePaginator $posts, int $page): bool
    {
        return $this->cache->tags(["posts"])
            ->put(
                "post.paginateByFilter.{$page}",
                $posts,
                $this->carbon->now()->addMinutes($this->minutes)
            );
    }
}
