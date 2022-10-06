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

namespace N1ebieski\ICore\Repositories\Page;

use Closure;
use InvalidArgumentException;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Franzose\ClosureTable\Extensions\Collection as ClosureTableCollection;

class PageRepo
{
    /**
     *
     * @param Page $page
     * @param Config $config
     * @param Auth $auth
     * @param App $app
     * @return void
     */
    public function __construct(
        protected Page $page,
        protected Config $config,
        protected Auth $auth,
        protected App $app
    ) {
        //
    }

    /**
     * [paginateByFilter description]
     * @param  array  $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->page->newQuery()
            ->selectRaw("`{$this->page->getTable()}`.*")
            ->when(!is_null($filter['search']), function (Builder|Page $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.pages.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach (['id'] as $attr) {
                                return $query->when(array_key_exists($attr, $this->page->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->page->getTable()}.{$attr}", $this->page->search[$attr]);
                                });
                            }
                        });
                    });
            })
            ->filterExcept($filter['except'])
            ->filterStatus($filter['status'])
            ->filterParent($filter['parent'])
            ->when(is_null($filter['orderby']), function (Builder|Page $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->when(is_null($filter['parent']), function (Builder|Page $query) {
                return $query->withAncestorsExceptSelf();
            })
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getAsTree description]
     * @return Collection [description]
     */
    public function getAsTree(): Collection
    {
        /** @var ClosureTableCollection */
        $pages = $this->page->newQuery()
            ->withAncestorsExceptSelf()
            ->orderBy('position', 'asc')
            ->get();

        return $pages->toTree();
    }

    /**
     * [getAsTreeExceptSelf description]
     * @return Collection [description]
     */
    public function getAsTreeExceptSelf(): Collection
    {
        /** @var Page */
        $self = $this->page->find($this->page->id);

        /** @var ClosureTableCollection */
        $pages = $this->page->whereNotIn('id', $self->descendants()->pluck('id')->toArray())
            ->withAncestorsExceptSelf()
            ->orderBy('position', 'asc')
            ->get();

        return $pages->toTree();
    }

    /**
     * [getAncestorsAsArray description]
     * @return array [description]
     */
    public function getAncestorsAsArray(): array
    {
        return $this->page->ancestors()->pluck('id')->toArray();
    }

    /**
     * [getDescendantsAsArray description]
     * @return array [description]
     */
    public function getDescendantsAsArray(): array
    {
        return $this->page->descendants()->pluck('id')->toArray();
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray(): array
    {
        return $this->page->getSiblings(['id', 'position'])
            ->pluck('position', 'id')
            ->toArray();
    }

    /**
     * [getWithChildrensByComponent description]
     * @param  array      $component [description]
     * @return Collect            [description]
     */
    public function getWithChildrensByComponent(array $component): Collect
    {
        return $this->page->newQuery()
            ->active()
            ->with(['childrens' => function (HasMany|Builder|Page $query) {
                return $query->active()->orderBy('position', 'asc');
            }])
            ->when(!is_null($component['pattern']), function (Builder|Page $query) use ($component) {
                $patternString = implode(', ', $component['pattern']);

                return $query->whereIn('id', $component['pattern'])
                    ->orderByRaw("FIELD(id, {$patternString}) ASC");
            }, function (Builder|Page $query) use ($component) {
                return $query->root()
                    ->limit($component['limit'])
                    ->orderBy('position', 'asc');
            })
            ->get()
            // @phpstan-ignore-next-line
            ->map(function (Page $page) {
                if ($page->childrens->isNotEmpty()) {
                    // @phpstan-ignore-next-line
                    $page->urls = $page->childrens->pluck('slug')->map(function ($page) {
                        return '*/' . $page;
                    });
                }

                return $page;
            });
    }

    /**
     * [getByComponent description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function getWithRecursiveChildrensByComponent(array $component): Collection
    {
        return $this->page->newQuery()
            ->when(!is_null($component['pattern']), function (Builder|Page $query) use ($component) {
                $patternString = implode(', ', $component['pattern']);

                return $query->whereIn('id', $component['pattern'])
                    ->orderByRaw("FIELD(id, ?) DESC", [$patternString]);
            }, function (Builder|Page $query) {
                return $query->root()->orderBy('position', 'asc');
            })
            ->active()
            ->withRecursiveAllRels()
            ->get();
    }

    /**
     * Route binding
     * @param  string $slug [description]
     * @return Page|null       [description]
     */
    public function firstBySlug(string $slug): ?Page
    {
        return $this->page->newQuery()
            ->where('slug', $slug)
            ->active()
            ->when(
                $this->app->make(MigrationUtil::class)->contains('create_stats_table'),
                function (Builder $query) {
                    return $query->with('stats');
                }
            )
            ->with('tags')
            ->withAncestorsExceptSelf()
            ->first();
    }

    /**
     *
     * @param array $filter
     * @return LengthAwarePaginator
     * @throws InvalidArgumentException
     */
    public function paginateCommentsByFilter(array $filter): LengthAwarePaginator
    {
        /** @var Comment */
        $comment = $this->page->comments()->make();

        // @phpstan-ignore-next-line
        return $this->page->comments()->where([
                ["{$comment->getTable()}.parent_id", null],
                ["{$comment->getTable()}.status", Status::ACTIVE]
            ])
            ->withAllRels($filter['orderby'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate($this->config->get('database.paginate'));
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
        return $this->page->newQuery()
            ->active()
            ->whereNotNull('content_html')
            ->withCount(['comments AS models_count' => function (MorphMany|Builder|Comment $query) {
                return $query->root()->active();
            }])
            ->chunk($chunk, $callback);
    }
}
