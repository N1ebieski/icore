<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Support\Carbon;

/**
 * [CategoryCache description]
 */
class CommentCache
{
    /**
     * Model
     * @var Comment
     */
    protected $comment;

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
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [protected description]
     * @var Carbon
     */
    protected $carbon;

    /**
     * [__construct description]
     * @param Comment        $comment        [description]
     * @param Cache          $cache          [description]
     * @param Config         $config         [description]
     * @param Collect        $collect        [description]
     * @param Carbon         $carbon         [description]
     */
    public function __construct(
        Comment $comment,
        Cache $cache,
        Config $config,
        Collect $collect,
        Carbon $carbon
    ) {
        $this->comment = $comment;

        $this->cache = $cache;
        $this->collect = $collect;
        $this->carbon = $carbon;

        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [rememberRootsByFilter description]
     * @param  array               $filter [description]
     * @param  int                  $page   [description]
     * @return LengthAwarePaginator         [description]
     */
    public function rememberRootsByFilter(array $filter, int $page) : LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems()) {
            $comments = $this->getRootsByFilter($page);
        }

        if (!isset($comments) || !$comments) {
            $comments = $this->comment->makeService()->getRootsByFilter($filter);

            if ($this->collect->make($filter)->isNullItems()) {
                $this->putRootsByFilter($comments, $page);
            }
        }

        return $comments;
    }

    /**
     * [getRootsByFilter description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getRootsByFilter(int $page) : ?LengthAwarePaginator
    {
        return $this->cache->tags([
            "comment.{$this->comment->poli}.{$this->comment->morph->id}"
        ])->get(
            "{$this->comment->poli}.getRootsByFilter.{$this->comment->morph->id}.{$page}"
        );
    }

    /**
     * [putRootsByFilter description]
     * @param  LengthAwarePaginator $comments [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putRootsByFilter(LengthAwarePaginator $comments, int $page) : bool
    {
        return $this->cache->tags([
            "comment.{$this->comment->poli}.{$this->comment->morph->id}"
        ])
        ->put(
            "{$this->comment->poli}.getRootsByFilter.{$this->comment->morph->id}.{$page}",
            $comments,
            $this->carbon->now()->addMinutes($this->minutes)
        );
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberByComponent(array $component) : Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['comments'])->remember(
            "comment.{$this->comment->poli}.getByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->comment->makeRepo()->getByComponent($component);
            }
        );
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberCountByModelTypeAndStatus() : Collection
    {
        return $this->cache->tags(['comments'])->remember(
            "comment.countByModelTypeAndStatus",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->comment->makeRepo()->countByModelTypeAndStatus();
            }
        );
    }
}
