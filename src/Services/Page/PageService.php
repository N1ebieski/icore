<?php

namespace N1ebieski\ICore\Services\Page;

use Throwable;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\PageLang\PageLang;
use N1ebieski\ICore\ValueObjects\Page\Status;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\MassAssignmentException;

class PageService
{
    /**
     * Undocumented function
     *
     * @param Page $page
     * @param Config $config
     * @param Collect $collect
     * @param DB $db
     */
    public function __construct(
        protected Page $page,
        protected Config $config,
        protected Collect $collect,
        protected DB $db
    ) {
        //
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
            ->paginate($filter['paginate'] ?? $this->config->get('database.paginate'));
    }

    /**
     *
     * @param array $attributes
     * @return Page
     * @throws Throwable
     */
    public function create(array $attributes): Page
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->page->fill($attributes);

            $this->page->user()->associate($attributes['user']);

            if ($attributes['parent_id'] !== null) {
                /** @var Page */
                $parent = $this->page->findOrFail($attributes['parent_id']);

                // If the parent is inactive, the child must inherit this state
                $this->page->status = $parent->status->isInactive() ?
                    Status::INACTIVE : $attributes['status'];
                $this->page->parent_id = $attributes['parent_id'];
            }

            $this->page->save();

            $this->page->currentLang->makeService()->create(
                array_merge($attributes, [
                    'page' => $this->page
                ])
            );

            if (array_key_exists('tags', $attributes)) {
                $this->page->tag($attributes['tags'] ?? []);
            }

            return $this->page;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Page
     * @throws Throwable
     */
    public function update(array $attributes): Page
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->page->fill($attributes);

            if ($attributes['parent_id'] != $this->page->parent_id) {
                if (is_null($attributes['parent_id'])) {
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

            $this->page->save();

            $this->page->currentLang->makeService()->createOrUpdate(
                array_merge($attributes, [
                    'page' => $this->page
                ])
            );

            return $this->page;
        });
    }

    /**
     *
     * @return bool
     * @throws Throwable
     */
    public function moveToRoot(): bool
    {
        return $this->db->transaction(function () {
            $this->page->makeRoot(0);

            return true;
        });
    }

    /**
     *
     * @param int $parent_id
     * @return bool
     * @throws Throwable
     */
    public function moveToParent(int $parent_id): bool
    {
        return $this->db->transaction(function () use ($parent_id) {
            /** @var Page */
            $parent = $this->page->findOrFail($parent_id);

            // In the case of changing the parent, we must prophylactically
            // change the status of the category (and descendants) to the same
            // as the parent to avoid the situation where the subcategory
            // is active and the parent is not.
            $this->page->update(['status' => $parent->status]);
            $this->page->descendants()->update(['status' => $parent->status]);

            $this->page->moveTo(0, $parent_id);

            return true;
        });
    }

    /**
     *
     * @param int $status
     * @return bool
     * @throws Throwable
     */
    public function updateStatus(int $status): bool
    {
        return $this->db->transaction(function () use ($status) {
            $update = $this->page->update(['status' => $status]);

            if ($update === true) {
                // Deactivates parent page, deactivates all descendants
                if ($this->page->status->isInactive()) {
                    $this->page->descendants()->update(['status' => $status]);
                }

                // Activating child page, activates all ancestors
                if ($this->page->status->isActive()) {
                    $this->page->ancestors()->update(['status' => $status]);
                }
            }

            return $update;
        });
    }

    /**
     *
     * @param int $position
     * @return bool
     * @throws Throwable
     */
    public function updatePosition(int $position): bool
    {
        return $this->db->transaction(function () use ($position) {
            return $this->page->update(['position' => $position]);
        });
    }

    /**
     *
     * @return null|bool
     * @throws Throwable
     */
    public function delete(): ?bool
    {
        return $this->db->transaction(function () {
            $this->page->comments()->delete();

            $this->page->tags()->detach();

            $this->page->stats()->detach();

            return $this->page->delete();
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
                /** @var Page|null */
                $p = $this->page->find($id);

                if (!is_null($p)) {
                    $p->makeService()->delete();

                    $deleted += 1;
                }
            }

            return $deleted;
        });
    }
}
