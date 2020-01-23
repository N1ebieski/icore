<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [CommentRepo description]
 */
class CategoryRepo
{
    /**
     * [private description]
     * @var Category
     */
    protected $category;

    /**
     * Config
     * @var int
     */
    protected $paginate;

    /**
     * [__construct description]
     * @param Category $category [description]
     * @param Config   $config   [description]
     */
    public function __construct(Category $category, Config $config)
    {
        $this->category = $category;
        $this->paginate = $config->get('database.paginate');
    }

    /**
     * [getByIds description]
     * @param  array      $ids [description]
     * @return Collection      [description]
     */
    public function getByIds(array $ids) : Collection
    {
        return $this->category->withAncestorsExceptSelf()
            ->whereIn('id', $ids)->get();
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->category->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->filterStatus($filter['status'])
            ->poliType()
            ->filterParent($filter['parent'])
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->when($filter['parent'] === null, function($query) {
                return $query->withAncestorsExceptSelf();
            })->filterPaginate($filter['paginate']);
    }

    /**
     * [getAsTree description]
     * @return Collection [description]
     */
    public function getAsTree() : Collection
    {
        return $this->category->getTreeByQuery(
            $this->category->poliType()->orderBy('position', 'asc')
        );
    }

    /**
     * [getAsTreeExceptSelf description]
     * @return Collection [description]
     */
    public function getAsTreeExceptSelf() : Collection
    {
        return $this->category->getTreeByQuery(
                $this->category->whereNotIn('id',
                    $this->category->find($this->category->id)
                    ->descendants()
                    ->get(['id'])
                    ->pluck('id')
                    ->toArray()
                )->poliType()
                ->orderBy('position', 'asc')
            );
    }

    /**
     * [getBySearch description]
     * @param  string     $name [description]
     * @return Collection|null       [description]
     */
    public function getBySearch(string $name)
    {
        if (!empty($name)) {
            return $this->category->withAncestorsExceptSelf()
                ->search($name)
                ->whereStatus(1)
                ->poliType()
                ->orderBy('real_depth', 'desc')
                ->limit(10)
                ->get();
        }
    }

    /**
     * [getAncestorsAsArray description]
     * @return array [description]
     */
    public function getAncestorsAsArray() : array
    {
        return $this->category->ancestors()->get(['id'])->pluck('id')->toArray();
    }

    /**
     * [getDescendantsAsArray description]
     * @return array [description]
     */
    public function getDescendantsAsArray() : array
    {
        return $this->category->descendants()->get(['id'])->pluck('id')->toArray();
    }

    /**
     * [getPosts description]
     * @return LengthAwarePaginator [description]
     */
    public function paginatePosts() : LengthAwarePaginator
    {
        return $this->category->morphs()
            ->active()
            ->with('user:id,name')
            ->orderBy('published_at', 'desc')
            ->paginate($this->paginate);
    }

    /**
     * [getWithRecursiveChildrens description]
     * @return Collection [description]
     */
    public function getWithRecursiveChildrens() : Collection
    {
        return $this->category->withRecursiveAllRels()
            ->withCount([
                'morphs' => function($query) {
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
        return $this->category->where('slug', '=', $slug)
            ->poliType()
            ->active()
            ->withAncestorsExceptSelf()
            ->first();
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray() : array
    {
        return $this->category->getSiblings(['id', 'position'])
            ->pluck('position', 'id')->toArray();
    }
}
