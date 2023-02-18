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
use RuntimeException;
use InvalidArgumentException;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Franzose\ClosureTable\Extensions\Collection as ClosureTableCollection;
use N1ebieski\ICore\Utils\Migration\Interfaces\MigrationRecognizeInterface;

class PageRepo
{
    /**
     *
     * @param Page $page
     * @param Config $config
     * @param Carbon $carbon
     * @param Auth $auth
     * @param App $app
     * @return void
     */
    public function __construct(
        protected Page $page,
        protected Config $config,
        protected Carbon $carbon,
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
            ->multiLang()
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
        $pages = $this->page->newQuery()
            ->selectRaw("`{$this->page->getTable()}`.*")
            ->multiLang()
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
        $pages = $this->page->newQuery()
            ->selectRaw("`{$this->page->getTable()}`.*")
            ->multiLang()
            ->whereNotIn("{$this->page->getTable()}.id", $self->descendants()->pluck('id')->toArray())
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
            ->selectRaw("`{$this->page->getTable()}`.*")
            ->multiLang()
            ->active()
            ->with(['childrens' => function (HasMany|Builder|Page $query) {
                return $query->selectRaw("`{$this->page->getTable()}`.*")
                    ->active()
                    ->multiLang()
                    ->orderBy('position', 'asc');
            }])
            ->when(!is_null($component['pattern']), function (Builder|Page $query) use ($component) {
                $patternString = implode(', ', $component['pattern']);

                return $query->whereIn("{$this->page->getTable()}.{$this->page->getKeyName()}", $component['pattern'])
                    ->orderByRaw("FIELD({$this->page->getTable()}.{$this->page->getKeyName()}, {$patternString}) ASC");
            }, function (Builder|Page $query) use ($component) {
                return $query->root()
                    ->limit($component['limit'])
                    ->orderBy('position', 'asc');
            })
            ->get()
            // @phpstan-ignore-next-line
            ->map(function (Page $page) {
                if ($page->childrens->isNotEmpty()) {
                    $urls = [];

                    $page->childrens->each(function (Page $page) use (&$urls) {
                        array_push($urls, '*/' . $page->currentLang->slug);
                    });

                    $page->urls = $urls;
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
            ->selectRaw("`{$this->page->getTable()}`.*")
            ->multiLang()
            ->when(!is_null($component['pattern']), function (Builder|Page $query) use ($component) {
                $patternString = implode(', ', $component['pattern']);

                return $query->whereIn("{$this->page->getTable()}.{$this->page->getKeyName()}", $component['pattern'])
                    ->orderByRaw("FIELD({$this->page->getTable()}.{$this->page->getKeyName()}, ?) DESC", [$patternString]);
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
            ->selectRaw("`{$this->page->getTable()}`.*")
            ->where('slug', $slug)
            ->multiLang()
            ->active()
            ->when(
                $this->app->make(MigrationRecognizeInterface::class)->contains('create_stats_table'),
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
        $comments = $this->page->comments();

        // @phpstan-ignore-next-line
        return $comments->active()
            ->lang()
            ->root()
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
            ->whereHas('langs', function (Builder $query) {
                return $query->whereNotNull('content_html');
            })
            ->with('langs')
            ->when(true, function (Builder $query) {
                foreach ($this->config->get('icore.multi_langs') as $lang) {
                    $query->withCount([
                        "comments AS models_count_{$lang}" => function (MorphMany|Builder|Comment $query) use ($lang) {
                            return $query->root()->active()->where('lang', $lang);
                        }
                    ]);
                }

                return $query;
            })
            ->chunk($chunk, $callback);
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
        return $this->page->newQuery()
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
