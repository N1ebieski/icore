<?php

namespace N1ebieski\ICore\Cache\Comment;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     * [protected description]
     * @var Config
     */
    protected $config;

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
     * [protected description]
     * @var Request
     */
    protected $request;

    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param Cache $cache
     * @param Config $config
     * @param Collect $collect
     * @param Carbon $carbon
     * @param Request $request
     */
    public function __construct(
        Comment $comment,
        Cache $cache,
        Config $config,
        Collect $collect,
        Carbon $carbon,
        Request $request
    ) {
        $this->comment = $comment;

        $this->cache = $cache;
        $this->config = $config;
        $this->collect = $collect;
        $this->carbon = $carbon;
        $this->request = $request;
    }

    /**
     * [rememberRootsByFilter description]
     * @param  array               $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function rememberRootsByFilter(array $filter): LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems()) {
            $comments = $this->getRootsByFilter($this->request->input('page'));
        }

        if (!isset($comments) || !$comments) {
            $comments = $this->comment->makeService()->getRootsByFilter($filter);

            if ($this->collect->make($filter)->isNullItems()) {
                $this->putRootsByFilter($comments, $this->request->input('page'));
            }
        }

        return $comments;
    }

    /**
     * [getRootsByFilter description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getRootsByFilter(): ?LengthAwarePaginator
    {
        return $this->cache->tags([
            "comment.{$this->comment->poli}.{$this->comment->morph->id}"
        ])->get(
            "{$this->comment->poli}.getRootsByFilter.{$this->comment->morph->id}.{$this->request->input('page')}"
        );
    }

    /**
     * [putRootsByFilter description]
     * @param  LengthAwarePaginator $comments [description]
     * @return bool                           [description]
     */
    public function putRootsByFilter(LengthAwarePaginator $comments): bool
    {
        return $this->cache->tags([
            "comment.{$this->comment->poli}.{$this->comment->morph->id}"
        ])
        ->put(
            "{$this->comment->poli}.getRootsByFilter.{$this->comment->morph->id}.{$this->request->input('page')}",
            $comments,
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes'))
        );
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['comments'])->remember(
            "comment.{$this->comment->poli}.getByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
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
    public function rememberCountByModelTypeAndStatus(): Collection
    {
        return $this->cache->tags(['comments'])->remember(
            "comment.countByModelTypeAndStatus",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                return $this->comment->makeRepo()->countByModelTypeAndStatus();
            }
        );
    }
}
