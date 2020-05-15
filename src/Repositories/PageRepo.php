<?php

namespace N1ebieski\ICore\Repositories;

use Closure;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [PageRepo description]
 */
class PageRepo
{
    /**
     * [private description]
     * @var Page
     */
    protected $page;

    /**
     * Config
     * @var int
     */
    protected $paginate;

    /**
     * [__construct description]
     * @param Page   $page   [description]
     * @param Config $config [description]
     */
    public function __construct(Page $page, Config $config)
    {
        $this->page = $page;

        $this->paginate = $config->get('database.paginate');
    }

    /**
     * [paginateByFilter description]
     * @param  array  $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->page->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->filterStatus($filter['status'])
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->filterParent($filter['parent'])
            ->when($filter['parent'] === null, function ($query) {
                return $query->withAncestorsExceptSelf();
            })->filterPaginate($filter['paginate']);
    }

    /**
     * [getAsTree description]
     * @return Collection [description]
     */
    public function getAsTree() : Collection
    {
        return $this->page->getTree();
    }

    /**
     * [getAsTreeExceptSelf description]
     * @return Collection [description]
     */
    public function getAsTreeExceptSelf() : Collection
    {
        return $this->page->getTreeByQuery(
            $this->page->whereNotIn(
                'id',
                $this->page->find($this->page->id)
                    ->descendants()
                    ->get(['id'])
                    ->pluck('id')
                    ->toArray()
            )
        );
    }

    /**
     * [getAncestorsAsArray description]
     * @return array [description]
     */
    public function getAncestorsAsArray() : array
    {
        return $this->page->ancestors()->get(['id'])->pluck('id')->toArray();
    }

    /**
     * [getDescendantsAsArray description]
     * @return array [description]
     */
    public function getDescendantsAsArray() : array
    {
        return $this->page->descendants()->get(['id'])->pluck('id')->toArray();
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray() : array
    {
        return $this->page->getSiblings(['id', 'position'])
            ->pluck('position', 'id')->toArray();
    }

    /**
     * [getWithChildrensByComponent description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function getWithChildrensByComponent(array $component) : Collection
    {
        return $this->page->active()
            ->with(['childrens' => function ($query) {
                $query->active()->orderBy('position', 'asc');
            }])
            ->when($component['pattern'] !== null, function ($query) use ($component) {
                $patternString = implode(', ', $component['pattern']);
                
                $query->whereIn('id', $component['pattern'])
                    ->orderByRaw("FIELD(id, ?) DESC", [$patternString]);
            }, function ($query) use ($component) {
                $query->root()
                    ->limit($component['limit'])
                    ->orderBy('position', 'asc');
            })
            ->get()
            ->map(function ($item) {
                if ($item->childrens->isNotEmpty()) {
                    $item->urls = $item->childrens->pluck('slug')->map(function ($item) {
                        return '*/' . $item;
                    });
                }

                return $item;
            });
    }

    /**
     * [getByComponent description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function getWithRecursiveChildrensByComponent(array $component) : Collection
    {
        return $this->page->withRecursiveAllRels()
            ->when($component['pattern'] !== null, function ($query) use ($component) {
                $patternString = implode(', ', $component['pattern']);
                
                $query->whereIn('id', $component['pattern'])
                    ->orderByRaw("FIELD(id, ?) DESC", [$patternString]);
            }, function ($query) {
                $query->root()->orderBy('position', 'asc');
            })
            ->active()
            ->get();
    }

    /**
     * Route binding
     * @param  string $slug [description]
     * @return Page|null       [description]
     */
    public function firstBySlug(string $slug) : ?Page
    {
        return $this->page->whereSlug($slug)
            ->active()
            ->withAncestorsExceptSelf()
            ->first();
    }

    /**
     * Comments belong to the Post model
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateCommentsByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->page->comments()->where([
                ['comments.parent_id', null],
                ['comments.status', Comment::ACTIVE]
            ])
            ->withAllRels($filter['orderby'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate($this->paginate);
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return boolean
     */
    public function chunkActiveWithModelsCount(Closure $callback) : bool
    {
        return $this->page->active()
            ->withCount(['comments AS models_count' => function ($query) {
                $query->root()->active();
            }])
            ->chunk(1000, $callback);
    }
}
