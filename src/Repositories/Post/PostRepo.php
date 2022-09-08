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

namespace N1ebieski\ICore\Repositories\Post;

use Closure;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\ValueObjects\Post\Status;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;

class PostRepo
{
    /**
     * Undocumented function
     *
     * @param Post $post
     * @param Config $config
     * @param Carbon $carbon
     * @param App $app
     * @param Auth $auth
     */
    public function __construct(
        protected Post $post,
        protected Config $config,
        protected Carbon $carbon,
        protected App $app,
        protected Auth $auth
    ) {
        //
    }

    /**
     * Comments belong to the Post model
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateCommentsByFilter(array $filter): LengthAwarePaginator
    {
        return $this->post->comments()
            ->active()
            ->root()
            ->withAllRels($filter['orderby'])
            ->filterExcept($filter['except'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate($this->config->get('database.paginate'));
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->post->selectRaw("`{$this->post->getTable()}`.*")
            ->with(['tags', 'user'])
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->when(
                $filter['status'] === null && !optional($this->auth->user())->can('admin.categories.view'),
                function ($query) {
                    $query->active();
                },
                function ($query) use ($filter) {
                    $query->filterStatus($filter['status']);
                }
            )
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->filterCategory($filter['category'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * Route binding
     * @param  string $slug [description]
     * @return Post|null       [description]
     */
    public function firstBySlug(string $slug)
    {
        return $this->post->where('slug', $slug)
            ->active()
            ->with([
                'categories' => function ($query) {
                    $query->withAncestorsExceptSelf()->active();
                },
                'user',
                'tags'
            ])
            ->when(
                $this->app->make(MigrationUtil::class)->contains('create_stats_table'),
                function ($query) {
                    $query->with('stats');
                }
            )
            ->first();
    }

    /**
     * [firstPrevious description]
     * @return Post|null [description]
     */
    public function firstPrevious()
    {
        return $this->post->active()
            ->where('id', '<', $this->post->id)
            ->orderBy('id', 'desc')
            ->first(['slug', 'title']);
    }

    /**
     * [firstNext description]
     * @return Post|null [description]
     */
    public function firstNext()
    {
        return $this->post->active()
            ->where('id', '>', $this->post->id)
            ->orderBy('id')
            ->first(['slug', 'title']);
    }

    /**
     * [getRelated description]
     * @param  int $limit [description]
     * @return Post|null         [description]
     */
    public function getRelated(int $limit = 5)
    {
        return $this->post->active()
            ->withAnyTags($this->post->tagList)
            ->where('posts.id', '<>', $this->post->id)
            ->limit($limit)
            ->inRandomOrder()
            ->get();
    }

    /**
     * [paginateArchiveByDate description]
     * @param  array                $date [description]
     * @return LengthAwarePaginator       [description]
     */
    public function paginateArchiveByDate(array $date): LengthAwarePaginator
    {
        return $this->post->with('user:id,name')
            ->active()
            ->whereRaw(
                'MONTH(published_at) = ? and YEAR(published_at) = ?',
                [(int)$date['month'], (int)$date['year']]
            )
            ->orderBy('published_at', 'desc')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [getArchives description]
     * @return Collection [description]
     */
    public function getArchives(): Collection
    {
        return $this->post->active()
            ->selectRaw('YEAR(published_at) year, MONTH(published_at) month, COUNT(*) posts_count')
            ->groupBy('year')
            ->groupBy('month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * [paginateByTag description]
     * @param  string               $tag [description]
     * @return LengthAwarePaginator      [description]
     */
    public function paginateByTag(string $tag): LengthAwarePaginator
    {
        return $this->post->withAllTags($tag)
            ->with('user:id,name')
            ->active()
            ->orderBy('published_at', 'desc')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [paginateLatest description]
     * @return LengthAwarePaginator [description]
     */
    public function paginateLatest(): LengthAwarePaginator
    {
        return $this->post->with('user:id,name')
            ->active()
            ->orderBy('published_at', 'desc')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [paginateBySearch description]
     * @param  string               $name [description]
     * @return LengthAwarePaginator       [description]
     */
    public function paginateBySearch(string $name): LengthAwarePaginator
    {
        return $this->post->selectRaw("`{$this->post->getTable()}`.*")
            ->with('user:id,name')
            ->from(
                $this->post->selectRaw("`{$this->post->getTable()}`.*")
                    ->search($name)
                    ->when($tag = $this->post->tags()->make()->findByName($name), function ($query) use ($tag) {
                        $query->unionAll(
                            $this->post->selectRaw('`posts`.*, 0 AS `title_relevance`, 0 AS `content_relevance`')
                                ->join('tags_models', function ($query) use ($tag) {
                                    $query->on('posts.id', '=', 'tags_models.model_id')
                                        ->where('tags_models.model_type', $this->post->getMorphClass())
                                        ->where('tags_models.tag_id', $tag->tag_id);
                                })
                                ->groupBy('posts.id')
                        );
                    }),
                'posts'
            )
            ->groupBy('posts.id')
            ->active()
            ->orderBySearch($name)
            ->orderBy('published_at', 'desc')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [updateActivateScheduled description]
     * @return int              [description]
     */
    public function activateScheduled(): int
    {
        return $this->post
            ->whereDate('published_at', '<', $this->carbon->now()->format('Y-m-d'))
            ->orWhere(function ($query) {
                $query->whereDate('published_at', '=', $this->carbon->now()->format('Y-m-d'))
                    ->whereTime('published_at', '<=', $this->carbon->now()->format('H:i:s'));
            })
            ->scheduled()
            ->update(['status' => Status::ACTIVE]);
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return boolean
     */
    public function chunkActiveWithModelsCount(Closure $callback): bool
    {
        return $this->post->active()
            ->withCount(['comments AS models_count' => function ($query) {
                $query->root()->active();
            }])
            ->chunk(1000, $callback);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function countByStatus(): Collection
    {
        return $this->post->selectRaw("`status`, COUNT(`id`) AS `count`")
            ->groupBy('status')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getLastActivity(): ?string
    {
        return optional(
            $this->post->active()
            ->orderBy('published_at', 'desc')
            ->first('published_at')
        )
        ->published_at;
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getLatestForHome(): Collection
    {
        return $this->post->active()
            ->latest()
            ->with(['user', 'categories', 'tags'])
            ->orderBy('published_at', 'desc')
            ->limit($this->config->get('icore.home.max'))
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param Builder|null $pages
     * @return Collection
     */
    public function countActiveByDateUnionPages(Builder $pages = null): Collection
    {
        return $this->post->selectRaw("YEAR(`post`.`created_at`) `year`, MONTH(`post`.`created_at`) `month`, 'posts' AS `type`, COUNT(*) AS `count`")
            ->from("{$this->post->getTable()} AS post")
            ->where('post.status', Status::ACTIVE)
            ->groupBy('year')
            ->groupBy('month')
            ->unionAll($pages)
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }
}
