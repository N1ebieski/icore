<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\FullUpdatable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;

/**
 * [PageService description]
 */
class PageService implements
    Creatable,
    Updatable,
    FullUpdatable,
    StatusUpdatable,
    PositionUpdatable,
    Deletable,
    GlobalDeletable
{
    /**
     * [protected description]
     * @var Page
     */
    protected $page;

    /**
     * [protected description]
     * @var int
     */
    protected $paginate;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [protected description]
     * @var Collection|null
     */
    protected $pages;

    /**
     * [__construct description]
     * @param Page   $page   [description]
     * @param Config $config [description]
     * @param Collect $collect
     */
    public function __construct(Page $page, Config $config, Collect $collect)
    {
        $this->page = $page;
        $this->collect = $collect;

        $this->paginate = (int)$config->get('database.paginate');
    }

    /**
     * Gets pages in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTree() : Collection
    {
        $this->pages = $this->page->makeRepo()->getAsTree();

        $this->pages = $this->flatten();

        return $this->pages;
    }

    /**
     * Gets pages except self in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTreeExceptSelf() : Collection
    {
        $this->pages = $this->page->makeRepo()->getAsTreeExceptSelf();

        $this->pages = $this->flatten();

        return $this->pages;
    }

    /**
     * Choose collection of pages
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        if ($this->collect->make($filter)->except(['paginate', 'except'])->isEmptyItems()) {
            return $this->getAsFlatTreeByFilter($filter);
        }

        return $this->page->makeRepo()->paginateByFilter($filter);
    }

    /**
     * Gets pages in flat collection with hierarchy order and paginate its
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function getAsFlatTreeByFilter(array $filter) : LengthAwarePaginator
    {
        $this->pages = $this->getAsFlatTree();

        return $this->pages->paginate($filter['paginate'] ?? $this->paginate);
    }

    /**
     * [flatten description]
     * @return Collection [description]
     */
    protected function flatten() : Collection
    {
        $pages = $this->pages->toArray();

        $pages = $this->flattenChildren($pages);

        $this->pages = $this->page->hydrate($pages);

        return $this->pages;
    }

    /**
     * Metoda pomocnicza. Spłaszcza rekursywnie tablicę children
     *
     * @param  array  $array
     * @return array
     */
    protected function flattenChildren(array $array) : array
    {
        $result = [];

        foreach ($array as $value) {
            if (is_array($value)) {
                $result[] = array_filter($value, function ($k) {
                    if ($k != 'children') {
                        return true;
                    }
                }, ARRAY_FILTER_USE_KEY);

                if (array_key_exists('children', $value)) {
                    $result = array_merge($result, $this->flattenChildren($value['children']));
                }
            }
        }

        return array_filter($result);
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->page->fill($attributes);
        $this->page->content = $this->page->content_html;
        $this->page->user()->associate(auth()->user());

        if ($attributes['parent_id'] !== null) {
            $parent = $this->page->findOrFail($attributes['parent_id']);
            // Jeśli rodzic jest nieaktywny, dziecko musi dziedziczyć ten stan
            $this->page->status = $parent->status === Page::INACTIVE ?
                Page::INACTIVE : $attributes['status'];
            $this->page->parent_id = $attributes['parent_id'];
        }

        $this->page->save();

        $this->page->tag($attributes['tags'] ?? []);

        return $this->page;
    }

    /**
     * Mini-Update the specified Page in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->page->title = $attributes['title'];
        $this->page->content_html = $attributes['content_html'];
        $this->page->content = $this->page->content_html;

        return $this->page->save();
    }

    /**
     * Full-Update the specified Page in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateFull(array $attributes) : bool
    {
        $this->page->fill(
            $this->collect->make($attributes)->except('parent_id')->toArray()
        );
        $this->page->content = $this->page->content_html;

        if ($attributes['parent_id'] != $this->page->parent_id) {
            if ($attributes['parent_id'] === null) {
                $this->moveToRoot();
            } else {
                $this->moveToParent($attributes['parent_id']);
            }
        }

        $this->page->retag($attributes['tags'] ?? []);

        return $this->page->save();
    }

    /**
     * [moveToRoot description]
     * @return void [description]
     */
    public function moveToRoot() : void
    {
        $this->page->makeRoot(0);
    }

    /**
     * [moveToParent description]
     * @param  int    $parent_id [description]
     * @return void            [description]
     */
    public function moveToParent(int $parent_id) : void
    {
        if ($parent = $this->page->findOrFail($parent_id)) {
            // W przypadku zmiany rodzica musimy profilaktycznie zmienic status kategorii
            // (i potomków) na taki jaki ma rodzic by uniknąć sytuacji gdy dziecko będzie akywne,
            // a rodzic nie.
            $this->page->update(['status' => $parent->status]);
            $this->page->descendants()->update(['status' => $parent->status]);

            $this->page->moveTo(0, $parent_id);
        }
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        $updateStatus = $this->page->update(['status' => $attributes['status']]);

        if ($updateStatus === true) {
            // Deaktywacja kategorii nadrzędnej, deaktywuje wszystkich potomków
            if ((int)$attributes['status'] === Page::INACTIVE) {
                $this->page->descendants()->update(['status' => $attributes['status']]);
            }

            // Aktywacja kategorii podrzędnej, aktywuje wszystkich przodków
            if ((int)$attributes['status'] === Page::ACTIVE) {
                $this->page->ancestors()->update(['status' => $attributes['status']]);
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
        return $this->page->update(['position' => $attributes['position']]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        $this->page->comments()->delete();

        $this->page->detag();

        $delete = $this->page->delete();

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
        return $this->page->where([
            ['parent_id', $this->page->parent_id],
            ['position', '>', $this->page->position]
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
            if ($p = $this->page->find($id)) {
                $p->makeService()->delete();

                $deleted += 1;
            }
        }

        return $deleted;
    }
}
