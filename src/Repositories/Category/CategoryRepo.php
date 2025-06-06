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
use RuntimeException;
use InvalidArgumentException;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Franzose\ClosureTable\Extensions\Collection as ClosureTableCollection;

class CategoryRepo
{
    /**
     *
     * @param Category $category
     * @param Config $config
     * @param Carbon $carbon
     * @param Auth $auth
     * @return void
     */
    public function __construct(
        protected Category $category,
        protected Config $config,
        protected Carbon $carbon,
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
            ->with('langs')
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
            ->multiLang()
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
            ->selectRaw("`{$this->category->getTable()}`.*")
            ->multiLang()
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
            ->selectRaw("`{$this->category->getTable()}`.*")
            ->whereNotIn('id', $this->category->descendants()->pluck('id')->toArray())
            ->multiLang()
            ->poliType()
            ->orderBy('position', 'asc')
            ->get();

        return $categories->toTree();
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
            ->multiLang()
            ->active()
            ->withAllRels()
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
            ->selectRaw("`{$this->category->getTable()}`.*")
            ->withRecursiveAllRels()
            ->withCount([
                'morphs' => function (MorphToMany|Builder|Category $query) {
                    return $query->active();
                }
            ])
            ->multiLang()
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
            ->selectRaw("`{$this->category->getTable()}`.*")
            ->multiLang()
            ->poliType()
            ->active()
            ->where('slug', $slug)
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
            ->with('langs')
            ->when(true, function (Builder $query) {
                foreach ($this->config->get('icore.multi_langs') as $lang) {
                    $query->withCount([
                        "morphs AS models_count_{$lang}" => function (mixed $query) use ($lang) {
                            return $query->active()->whereHas('langs', function (mixed $query) use ($lang) {
                                return $query->where('lang', $lang);
                            });
                        }
                    ]);
                }

                return $query;
            })
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

    /**
     *
     * @param Closure $closure
     * @param string $timestamp
     * @return bool
     * @throws RuntimeException
     */
    public function chunkAutoTransWithLangsByTranslatedAt(
        Closure $closure,
        string $timestamp
    ): bool {
        return $this->category->newQuery()
            ->autoTrans()
            ->whereHas('langs', function (Builder $query) {
                return $query->where('progress', 100);
            })
            ->where(function (Builder $query) use ($timestamp) {
                return $query->whereHas('langs', function (Builder $query) use ($timestamp) {
                    return $query->where('progress', 0)
                        ->where(function (Builder $query) use ($timestamp) {
                            return $query->whereDate(
                                'translated_at',
                                '<',
                                $this->carbon->parse($timestamp)->format('Y-m-d')
                            )
                            ->orWhere(function (Builder $query) use ($timestamp) {
                                return $query->whereDate(
                                    'translated_at',
                                    '=',
                                    $this->carbon->parse($timestamp)->format('Y-m-d')
                                )
                                ->whereTime(
                                    'translated_at',
                                    '<=',
                                    $this->carbon->parse($timestamp)->format('H:i:s')
                                );
                            })
                            ->orWhere('translated_at', null);
                        });
                })
                ->orWhere(function (Builder $query) {
                    foreach ($this->config->get('icore.multi_langs') as $lang) {
                        $query->orWhereDoesntHave('langs', function (Builder $query) use ($lang) {
                            return $query->where('lang', $lang);
                        });
                    }

                    return $query;
                });
            })
            ->chunk(1000, $closure);
    }
}
