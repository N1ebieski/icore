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

namespace N1ebieski\ICore\Repositories\Category;

use Closure;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Franzose\ClosureTable\Extensions\Collection as ClosureTableCollection;

class CategoryRepo
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Config $config
     * @param Auth $auth
     */
    public function __construct(
        protected Category $category,
        protected Config $config,
        protected Auth $auth
    ) {
        //
    }

    /**
     * [getByIds description]
     * @param  array      $ids [description]
     * @return Collection      [description]
     */
    public function getByIds(array $ids): Collection
    {
        return $this->category->withAncestorsExceptSelf()
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->category->newQuery()
            ->selectRaw("`{$this->category->getTable()}`.*")
            ->when(
                is_null($filter['status']) && !$this->auth->user()?->can('admin.categories.view'),
                function (Builder|Category $query) {
                    return $query->active();
                },
                function (Builder|Category $query) use ($filter) {
                    return $query->filterStatus($filter['status']);
                }
            )
            ->poliType()
            ->when(!is_null($filter['search']), function (Builder|Category $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.categories.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach (['id'] as $attr) {
                                $query = $query->when(array_key_exists($attr, $this->category->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->category->getTable()}.{$attr}", $this->category->search[$attr]);
                                });
                            }

                            return $query;
                        });
                    });
            })
            ->filterExcept($filter['except'])
            ->filterParent($filter['parent'])
            ->when(is_null($filter['orderby']), function (Builder|Category $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->when(!is_null($filter['search']), function (Builder|Category $query) {
                return $query->orderByRaw("LENGTH(`{$this->category->getTable()}`.`name`)");
            })
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->withAncestorsExceptSelf()
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getAsTree description]
     * @return Collection [description]
     */
    public function getAsTree(): Collection
    {
        /** @var ClosureTableCollection */
        $categories = $this->category->newQuery()
            ->poliType()
            ->orderBy('position', 'asc')
            ->get();

        return $categories->toTree();
    }

    /**
     * [getAsTreeExceptSelf description]
     * @return Collection [description]
     */
    public function getAsTreeExceptSelf(): Collection
    {
        /** @var ClosureTableCollection */
        $categories = $this->category->newQuery()
            ->whereNotIn('id', $this->category->descendants()->pluck('id')->toArray())
            ->poliType()
            ->orderBy('position', 'asc')
            ->get();

        return $categories->toTree();
    }

    /**
     *
     * @param string $name
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function getBySearch(string $name): Collection
    {
        return $this->category->newQuery()
            ->withAncestorsExceptSelf()
            ->search($name)
            ->active()
            ->poliType()
            ->orderBy('real_depth', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * [getAncestorsAsArray description]
     * @return array [description]
     */
    public function getAncestorsAsArray(): array
    {
        return $this->category->ancestors()->pluck('id')->toArray();
    }

    /**
     * [getDescendantsAsArray description]
     * @return array [description]
     */
    public function getDescendantsAsArray(): array
    {
        return $this->category->descendants()->pluck('id')->toArray();
    }

    /**
     * [getPosts description]
     * @return LengthAwarePaginator [description]
     */
    public function paginatePosts(): LengthAwarePaginator
    {
        return $this->category->morphs()
            ->active()
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->paginate($this->config->get('database.paginate'));
    }

    /**
     * [getWithRecursiveChildrens description]
     * @return Collection [description]
     */
    public function getWithRecursiveChildrens(): Collection
    {
        return $this->category->newQuery()
            ->withRecursiveAllRels()
            ->withCount([
                'morphs' => function ($query) {
                    return $query->active();
                }
            ])
            ->poliType()
            ->active()
            ->root()
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Category|null       [description]
     */
    public function firstBySlug(string $slug): ?Category
    {
        return $this->category->newQuery()
            ->where('slug', $slug)
            ->poliType()
            ->active()
            ->withAncestorsExceptSelf()
            ->first();
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray(): array
    {
        return $this->category->getSiblings(['id', 'position'])
            ->pluck('position', 'id')
            ->toArray();
    }

    /**
     *
     * @param int $chunk
     * @param Closure $closure
     * @return bool
     * @throws InvalidArgumentException
     */
    public function chunkActiveWithModelsCount(int $chunk, Closure $closure): bool
    {
        return $this->category->newQuery()
            ->active()
            ->poliType()
            ->withCount(['morphs AS models_count' => function ($query) {
                $query->active();
            }])
            ->chunk($chunk, $closure);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function countByStatus(): Collection
    {
        return $this->category->newQuery()
            ->selectRaw("`status`, COUNT(`id`) AS `count`")
            ->poliType()
            ->groupBy('status')
            ->get();
    }
}
