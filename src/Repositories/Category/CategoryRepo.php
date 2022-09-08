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
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;

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
        return $this->category->selectRaw("`{$this->category->getTable()}`.*")
            ->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->when(
                $filter['status'] === null && !optional($this->auth->user())->can('admin.categories.view'),
                function ($query) {
                    return $query->active();
                },
                function ($query) use ($filter) {
                    return $query->filterStatus($filter['status']);
                }
            )
            ->poliType()
            ->filterParent($filter['parent'])
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
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
        /**
         * @phpstan-ignore-next-line
         */
        return  $this->category->poliType()
            ->orderBy('position', 'asc')
            ->get()
            ->toTree();
    }

    /**
     * [getAsTreeExceptSelf description]
     * @return Collection [description]
     */
    public function getAsTreeExceptSelf(): Collection
    {
        /**
         * @phpstan-ignore-next-line
         */
        return $this->category->whereNotIn(
            'id',
            $this->category->descendants()
                ->get(['id'])
                ->pluck('id')
                ->toArray()
        )
        ->poliType()
        ->orderBy('position', 'asc')
        ->get()
        ->toTree();
    }

    /**
     * [getBySearch description]
     * @param  string     $name [description]
     * @return Collection|null       [description]
     */
    public function getBySearch(string $name)
    {
        return $this->category->withAncestorsExceptSelf()
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
        return $this->category->ancestors()
            ->get(['id'])
            ->pluck('id')
            ->toArray();
    }

    /**
     * [getDescendantsAsArray description]
     * @return array [description]
     */
    public function getDescendantsAsArray(): array
    {
        return $this->category->descendants()
            ->get(['id'])
            ->pluck('id')
            ->toArray();
    }

    /**
     * [getPosts description]
     * @return LengthAwarePaginator [description]
     */
    public function paginatePosts(): LengthAwarePaginator
    {
        if (!method_exists($this->category, 'morphs')) {
            throw new \Exception('Entity must be polimorphic.');
        }

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
        return $this->category->withRecursiveAllRels()
            ->withCount([
                'morphs' => function ($query) {
                    $query->active();
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
    public function firstBySlug(string $slug)
    {
        return $this->category->where('slug', $slug)
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
     * Undocumented function
     *
     * @param Closure $closure
     * @return bool
     */
    public function chunkActiveWithModelsCount(Closure $closure): bool
    {
        return $this->category->active()
            ->poliType()
            ->withCount(['morphs AS models_count' => function ($query) {
                $query->active();
            }])
            ->chunk(1000, $closure);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function countByStatus(): Collection
    {
        return $this->category->poliType()
            ->selectRaw("`status`, COUNT(`id`) AS `count`")
            ->groupBy('status')
            ->get();
    }
}
