<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as Collect;

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
     * [__construct description]
     * @param Comment        $comment        [description]
     * @param Cache          $cache          [description]
     * @param Config         $config         [description]
     * @param Collect        $collect        [description]
     */
    public function __construct(
        Comment $comment,
        Cache $cache,
        Config $config,
        Collect $collect
    )
    {
        $this->comment = $comment;
        $this->cache = $cache;
        $this->collect = $collect;
        $this->minutes = $config->get('icore.cache.minutes');
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
            $comments = $this->comment->getService()->getRootsByFilter($filter);

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
            'comments.'.$this->comment->poli.'.'.$this->comment->getMorph()->id
        ])->get(
            $this->comment->poli.'.getRootsByFilter.'.$this->comment->getMorph()->id.'.'.$page
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
            'comments.'.$this->comment->poli.'.'.$this->comment->getMorph()->id
        ])
        ->put(
            $this->comment->poli.'.getRootsByFilter.'.$this->comment->getMorph()->id.'.'.$page,
            $comments,
            now()->addMinutes($this->minutes)
        );
    }
}
