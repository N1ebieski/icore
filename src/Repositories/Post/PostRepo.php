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
use InvalidArgumentException;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\ValueObjects\Post\Status;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Utils\Migration\Interfaces\MigrationRecognizeInterface;

class PostRepo
{
    /**
     * Undocumented function
     *
     * @param Post $post
     * @param Config $config
     * @param App $app
     * @param Auth $auth
     */
    public function __construct(
        protected Post $post,
        protected Config $config,
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
        /** @var Comment */
        $comments = $this->post->comments();

        // @phpstan-ignore-next-line
        return $comments->active()
            ->lang()
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
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->multiLang()
            ->when(
                is_null($filter['status']) && !$this->auth->user()?->can('admin.categories.view'),
                function (Builder|Post $query) {
                    return $query->active();
                },
                function (Builder|Post $query) use ($filter) {
                    return $query->filterStatus($filter['status']);
                }
            )
            ->filterExcept($filter['except'])
            ->when(!is_null($filter['search']), function (Builder|Post $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.posts.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach (['id'] as $attr) {
                                return $query->when(array_key_exists($attr, $this->post->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->post->getTable()}.{$attr}", $this->post->search[$attr]);
                                });
                            }
                        });
                    });
            })
            ->filterCategory($filter['category'])
            ->when(is_null($filter['orderby']), function (Builder|Post $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->withAllRels()
            ->filterPaginate($filter['paginate']);
    }

    /**
     * Route binding
     * @param  string $slug [description]
     * @return Post|null       [description]
     */
    public function firstBySlug(string $slug): ?Post
    {
        // @phpstan-ignore-next-line
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->where('slug', $slug)
            ->multiLang()
            ->active()
            ->withAllRels([
                'categories' => function (MorphToMany|Builder|Category $query) {
                    /** @var Category */
                    $category = $query->getModel();

                    return $query->selectRaw("`{$category->getTable()}`.*")
                        ->withAncestorsExceptSelf()
                        ->multiLang()
                        ->active();
                }
            ])
            ->when(
                $this->app->make(MigrationRecognizeInterface::class)->contains('create_stats_table'),
                function (Builder $query) {
                    return $query->with('stats');
                }
            )
            ->first();
    }

    /**
     * [firstPrevious description]
     * @return Post|null [description]
     */
    public function firstPrevious(): ?Post
    {
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->multiLang()
            ->active()
            ->where("{$this->post->getTable()}.id", '<', $this->post->id)
            ->orderBy("{$this->post->getTable()}.id", 'desc')
            ->first();
    }

    /**
     * [firstNext description]
     * @return Post|null [description]
     */
    public function firstNext(): ?Post
    {
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->multiLang()
            ->active()
            ->where("{$this->post->getTable()}.id", '>', $this->post->id)
            ->orderBy("{$this->post->getTable()}.id")
            ->first();
    }

    /**
     *
     * @param int $limit
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function getRelated(int $limit = 5): Collection
    {
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->multiLang()
            ->active()
            ->withAnyTags($this->post->tagList)
            ->where("{$this->post->getTable()}.{$this->post->getKeyName()}", '<>', $this->post->id)
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
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->multiLang()
            ->active()
            ->whereRaw(
                'MONTH(published_at) = ? and YEAR(published_at) = ?',
                [(int)$date['month'], (int)$date['year']]
            )
            ->orderBy('published_at', 'desc')
            ->with('user:id,name')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [getArchives description]
     * @return Collection [description]
     */
    public function getArchives(): Collection
    {
        return $this->post->newQuery()
            ->selectRaw('YEAR(published_at) year, MONTH(published_at) month, COUNT(*) posts_count')
            ->multiLang()
            ->active()
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
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->withAnyTags($tag)
            ->multiLang()
            ->active()
            ->orderBy('published_at', 'desc')
            ->with('user:id,name')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [paginateLatest description]
     * @return LengthAwarePaginator [description]
     */
    public function paginateLatest(): LengthAwarePaginator
    {
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->multiLang()
            ->active()
            ->orderBy('published_at', 'desc')
            ->with('user:id,name')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [paginateBySearch description]
     * @param  string               $name [description]
     * @return LengthAwarePaginator       [description]
     */
    public function paginateBySearch(string $name): LengthAwarePaginator
    {
        /** @var Tag */
        $tag = $this->post->tags()->make();

        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->from(
                $this->post->newQuery()
                    ->selectRaw("`{$this->post->getTable()}`.*")
                    ->multiLang()
                    ->search($name)
                    ->when($tag = $tag->findByName($name), function (Builder $query) use ($tag) {
                        // @phpstan-ignore-next-line
                        return $query->unionAll(
                            $this->post->newQuery()
                                ->selectRaw('`posts`.*, 0 AS `title_relevance`, 0 AS `content_relevance`')
                                ->join('tags_models', function (JoinClause $query) use ($tag) {
                                    return $query->on('posts.id', '=', 'tags_models.model_id')
                                        ->where('tags_models.model_type', $this->post->getMorphClass())
                                        // @phpstan-ignore-next-line
                                        ->where('tags_models.tag_id', $tag->tag_id);
                                })
                                ->groupBy('posts.id')
                                ->getQuery()
                        );
                    })->getQuery(),
                'posts'
            )
            ->active()
            ->groupBy('posts.id')
            ->orderBySearch($name)
            ->orderBy('published_at', 'desc')
            ->withAllRels()
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     *
     * @param int $chunk
     * @param Closure $callback
     * @return bool
     * @throws InvalidArgumentException
     */
    public function chunkActiveWithModelsCount(int $chunk, Closure $callback): bool
    {
        return $this->post->newQuery()
            ->active()
            ->withCount(['comments AS models_count' => function (MorphMany|Builder|Comment $query) {
                $query->root()->active();
            }])
            ->with('langs')
            ->chunk($chunk, $callback);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function countByStatus(): Collection
    {
        return $this->post->newQuery()
            ->selectRaw("`status`, COUNT(`id`) AS `count`")
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
            $this->post->newQuery()
                ->active()
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
        return $this->post->newQuery()
            ->selectRaw("`{$this->post->getTable()}`.*")
            ->multiLang()
            ->active()
            ->latest()
            ->orderBy('published_at', 'desc')
            ->limit($this->config->get('icore.home.max'))
            ->withAllRels()
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
        return $this->post->newQuery()
            ->selectRaw("YEAR(`post`.`created_at`) `year`, MONTH(`post`.`created_at`) `month`, 'posts' AS `type`, COUNT(*) AS `count`")
            ->from("{$this->post->getTable()} AS post")
            ->where('post.status', Status::ACTIVE)
            ->groupBy('year')
            ->groupBy('month')
            ->when(!is_null($pages), function (Builder $query) use ($pages) {
                // @phpstan-ignore-next-line
                return $query->unionAll($pages->getQuery());
            })
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }
}
