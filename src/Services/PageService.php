<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Services\Interfaces\FullUpdatable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;

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
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Page $page
     * @param Config $config
     * @param Collect $collect
     * @param Auth $auth
     * @param DB $db
     */
    public function __construct(
        Page $page,
        Config $config,
        Collect $collect,
        Auth $auth,
        DB $db
    ) {
        $this->page = $page;

        $this->collect = $collect;
        $this->auth = $auth;
        $this->db = $db;

        $this->paginate = (int)$config->get('database.paginate');
    }

    /**
     * Gets pages in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTree(): Collection
    {
        return $this->page->makeRepo()
            ->getAsTree()
            ->flattenRelation('children');
    }

    /**
     * Gets pages except self in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTreeExceptSelf(): Collection
    {
        return $this->page->makeRepo()
            ->getAsTreeExceptSelf()
            ->flattenRelation('children');
    }

    /**
     * Choose collection of pages
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
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
    public function getAsFlatTreeByFilter(array $filter): LengthAwarePaginator
    {
        return $this->getAsFlatTree()
            ->whereNotIn('id', $filter['except'])
            ->paginate($filter['paginate'] ?? $this->paginate);
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->page->fill($attributes);
            $this->page->content = $this->page->content_html;
            $this->page->user()->associate($this->auth->user());

            if ($attributes['parent_id'] !== null) {
                $parent = $this->page->findOrFail($attributes['parent_id']);
                // If the parent is inactive, the child must inherit this state
                $this->page->status = $parent->status === Page::INACTIVE ?
                    Page::INACTIVE : $attributes['status'];
                $this->page->parent_id = $attributes['parent_id'];
            }

            $this->page->save();

            if (array_key_exists('tags', $attributes)) {
                $this->page->tag($attributes['tags'] ?? []);
            }

            return $this->page;
        });
    }

    /**
     * Mini-Update the specified Page in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->page->title = $attributes['title'];
            $this->page->content_html = $attributes['content_html'];
            $this->page->content = $this->page->content_html;

            return $this->page->save();
        });
    }

    /**
     * Full-Update the specified Page in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateFull(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
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

            if (array_key_exists('user', $attributes)) {
                $this->page->user()->associate($attributes['user']);
            }

            if (array_key_exists('tags', $attributes)) {
                $this->page->retag($attributes['tags'] ?? []);
            }

            return $this->page->save();
        });
    }

    /**
     * [moveToRoot description]
     * @return void [description]
     */
    public function moveToRoot(): void
    {
        $this->db->transaction(function () {
            $this->page->makeRoot(0);
        });
    }

    /**
     * [moveToParent description]
     * @param  int    $parent_id [description]
     * @return void            [description]
     */
    public function moveToParent(int $parent_id): void
    {
        $this->db->transaction(function () use ($parent_id) {
            if ($parent = $this->page->findOrFail($parent_id)) {
                // In the case of changing the parent, we must prophylactically
                // change the status of the category (and descendants) to the same
                // as the parent to avoid the situation where the subcategory
                // is active and the parent is not.
                $this->page->update(['status' => $parent->status]);
                $this->page->descendants()->update(['status' => $parent->status]);

                $this->page->moveTo(0, $parent_id);
            }
        });
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $update = $this->page->update(['status' => $attributes['status']]);

            if ($update === true) {
                // Deactivates parent page, deactivates all descendants
                if ((int)$attributes['status'] === Page::INACTIVE) {
                    $this->page->descendants()->update(['status' => $attributes['status']]);
                }

                // Activating child page, activates all ancestors
                if ((int)$attributes['status'] === Page::ACTIVE) {
                    $this->page->ancestors()->update(['status' => $attributes['status']]);
                }
            }

            return $update;
        });
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->page->update(['position' => $attributes['position']]);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            $this->page->comments()->delete();

            $this->page->detag();

            $this->page->stats()->detach();

            $delete = $this->page->delete();

            if ($delete === true) {
                // Decrement position of siblings by 1. ClosureTable has a bug and doesn't
                // do it automatically
                $this->decrement();
            }

            return $delete;
        });
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids): int
    {
        return $this->db->transaction(function () use ($ids) {
            $deleted = 0;

            foreach ($ids as $id) {
                if ($p = $this->page->find($id)) {
                    $p->makeService()->delete();

                    $deleted += 1;
                }
            }

            return $deleted;
        });
    }

    /**
     * Decrement position of siblings by 1. ClosureTable has a bug and doesn't
     * do it automatically
     * @return bool [description]
     */
    private function decrement(): bool
    {
        return $this->db->transaction(function () {
            return $this->page->where([
                ['parent_id', $this->page->parent_id],
                ['position', '>', $this->page->position]
            ])->decrement('position');
        });
    }
}
