<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;

/**
 * [CategoryService description]
 */
class CategoryService implements
    Creatable,
    Updatable,
    StatusUpdatable,
    PositionUpdatable,
    Deletable,
    GlobalDeletable
{
    /**
     * Model
     * @var Category
     */
    protected $category;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * Config
     * @var int
     */
    protected $paginate;

    /**
     * Collection of categories
     * @var Collection|LengthAwarePaginator
     */
    protected $categories;

    /**
     * [__construct description]
     * @param Category     $category     [description]
     * @param Config       $config       [description]
     * @param Collect $collect
     */
    public function __construct(Category $category, Config $config, Collect $collect)
    {
        $this->category = $category;
        $this->collect = $collect;

        $this->paginate = $config->get('database.paginate');
    }

    /**
     * Gets categories in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTree() : Collection
    {
        $this->categories = $this->category->makeRepo()->getAsTree();

        return $this->categories->flattenRelation('children');
    }

    /**
     * Choose collection of categories
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        if ($this->collect->make($filter)->except(['paginate', 'except'])->isEmptyItems()) {
            return $this->getAsFlatTreeByFilter($filter);
        }

        return $this->category->makeRepo()->paginateByFilter($filter);
    }

    /**
     * Gets categories in flat collection with hierarchy order and paginate its
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function getAsFlatTreeByFilter(array $filter) : LengthAwarePaginator
    {
        $this->categories = $this->getAsFlatTree();

        return $this->categories->whereNotIn('id', $filter['except'])
            ->paginate($filter['paginate'] ?? $this->paginate);
    }

    /**
     * Gets categories except self in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTreeExceptSelf() : Collection
    {
        $this->categories = $this->category->makeRepo()->getAsTreeExceptSelf();

        return $this->categories->flattenRelation('children');
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->category->name = $attributes['name'];
        $this->category->icon = $attributes['icon'] ?? null;

        if ($attributes['parent_id'] !== null) {
            $parent = $this->category->findOrFail($attributes['parent_id']);
            $this->category->status = $parent->status;
            $this->category->parent_id = $attributes['parent_id'];
        }

        $this->category->save();

        return $this->category;
    }

    /**
     * [createGlobal description]
     * @param  array      $attributes [description]
     * @return Collection             [description]
     */
    public function createGlobal(array $attributes) : Collection
    {
        if ($attributes['parent_id'] !== null) {
            $parent_id = $this->category->find($attributes['parent_id']);
        }

        if (isset($attributes['clear'])) {
            if ((bool)$attributes['clear'] === true) {
                $this->category->newQuery()->delete();
                $parent_id = null;
            }
        }

        return $this->category->createFromArray(
            json_decode($attributes['names'], true),
            $parent_id ?? null
        );
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        $delete = $this->category->delete();
        // $this->category->deleteSubtree(true, true);

        if ($delete === true) {
            // Zmniejszamy pozycje rodzeństwa o jeden bo ClosureTable nie robi tego
            // z automatu podczas usuwania (nie wiem czemu?)
            $this->decrement();
        }

        return $delete;
    }

    /**
     * Decrement position of siblings by 1. ClosureTable has a bug and doesn't
     * do it automatically
     * @return bool [description]
     */
    private function decrement() : bool
    {
        return $this->category->where([
            ['parent_id', $this->category->parent_id],
            ['position', '>', $this->category->position]
        ])->decrement('position');
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {
        $deleted = 0;
        // Antywzorzec, ale nie mialem wyboru, bo ClosureTable nie zmienia pozycji
        // rodzeństwa o 1 podczas usuwania i trzeba to robić ręcznie po każdym usunięciu
        foreach ($ids as $id) {
            if ($c = $this->category->find($id)) {
                $c->makeService()->delete();

                $deleted += 1;
            }
        }

        return $deleted;
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        $updateStatus = $this->category->update(['status' => $attributes['status']]);

        if ($updateStatus === true) {
            // Deaktywacja kategorii nadrzędnej, deaktywuje wszystkich potomków
            if ($attributes['status'] == Category::INACTIVE) {
                $this->category->descendants()->update(['status' => $attributes['status']]);
            }

            // Aktywacja kategorii podrzędnej, aktywuje wszystkich przodków
            if ($attributes['status'] == Category::ACTIVE) {
                $this->category->ancestors()->update(['status' => $attributes['status']]);
            }
        }

        return $updateStatus;
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool
    {
        return $this->category->update(['position' => $attributes['position']]);
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $update = $this->category->update([
            'name' => $attributes['name'],
            'icon' => $attributes['icon'] ?? null
        ]);

        if ($attributes['parent_id'] != $this->category->parent_id) {
            if ($attributes['parent_id'] === null) {
                $this->moveToRoot();
            } else {
                $this->moveToParent($attributes['parent_id']);
            }
        }

        return $update;
    }

    /**
     * [moveToRoot description]
     * @return void [description]
     */
    public function moveToRoot() : void
    {
        $this->category->makeRoot(0);
    }

    /**
     * [moveToParent description]
     * @param  int    $parent_id [description]
     * @return void            [description]
     */
    public function moveToParent(int $parent_id) : void
    {
        if ($parent = $this->category->findOrFail($parent_id)) {
            // W przypadku zmiany rodzica musimy profilaktycznie zmienic status kategorii
            // (i potomków) na taki jaki ma rodzic by uniknąć sytuacji gdy kategoria podrzędna
            // będzie aktywna a nadrzędne nie.
            $this->category->update(['status' => $parent->status]);
            $this->category->descendants()->update(['status' => $parent->status]);

            $this->category->moveTo(0, $parent_id);
        }
    }
}
