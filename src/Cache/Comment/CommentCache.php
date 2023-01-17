<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
        protected Comment $comment,
        protected Cache $cache,
        protected Config $config,
        protected Collect $collect,
        protected Carbon $carbon,
        protected Request $request
    ) {
        //
    }

    /**
     * [rememberRootsByFilter description]
     * @param  array<string, string> $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function rememberRootsByFilter(array $filter): LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems()) {
            $comments = $this->getRootsByFilter();
        }

        if (!isset($comments)) {
            $comments = $this->comment->makeService()->getRootsByFilter($filter);

            if ($this->collect->make($filter)->isNullItems()) {
                $this->putRootsByFilter($comments);
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
     * @return Collect
     */
    public function rememberByComponent(array $component): Collect
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
