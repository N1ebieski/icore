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

namespace N1ebieski\ICore\Services\Category;

use Throwable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\ValueObjects\Category\Status;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Config $config
     * @param Collect $collect
     * @param DB $db
     */
    public function __construct(
        protected Category $category,
        protected Config $config,
        protected Collect $collect,
        protected DB $db
    ) {
        //
    }

    /**
     * Gets categories in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTree(): Collection
    {
        return $this->category->makeRepo()
            ->getAsTree()
            ->flattenRelation('children');
    }

    /**
     * Choose collection of categories
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
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
    public function getAsFlatTreeByFilter(array $filter): LengthAwarePaginator
    {
        return $this->getAsFlatTree()
            ->whereNotIn('id', $filter['except'])
            ->paginate($filter['paginate'] ?? $this->config->get('database.paginate'));
    }

    /**
     * Gets categories except self in flat collection with hierarchy order
     * @return Collection [description]
     */
    public function getAsFlatTreeExceptSelf(): Collection
    {
        return $this->category->makeRepo()
            ->getAsTreeExceptSelf()
            ->flattenRelation('children');
    }

    /**
     *
     * @param array $attributes
     * @return Category
     * @throws Throwable
     */
    public function create(array $attributes): Category
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->category->name = $attributes['name'];
            $this->category->icon = $attributes['icon'] ?? null;

            if ($attributes['parent_id'] !== null) {
                /**
                 * @var Category $parent
                 */
                $parent = $this->category->findOrFail($attributes['parent_id']);

                $this->category->status = $parent->status;
                $this->category->parent_id = $attributes['parent_id'];
            }

            $this->category->save();

            return $this->category;
        });
    }

    /**
     * [createGlobal description]
     * @param  array      $attributes [description]
     * @return Collection             [description]
     */
    public function createGlobal(array $attributes): Collection
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($attributes['parent_id'] !== null) {
                /**
                 * @var Category $parent
                 */
                $parent = $this->category->find($attributes['parent_id']);
            }

            if (isset($attributes['clear'])) {
                if ((bool)$attributes['clear'] === true) {
                    $this->category->newQuery()->poliType()->delete();

                    $parent = null;
                }
            }

            return $this->category->createFromArray(
                json_decode($attributes['names'], true),
                $parent ?? null
            );
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
            return $this->category->delete();
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
                if (($c = $this->category->find($id)) instanceof Category) {
                    $c->makeService()->delete();

                    $deleted += 1;
                }
            }

            return $deleted;
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
            $update = $this->category->update(['status' => $status]);

            if ($update === true) {
                // Deactivates parent category, deactivates all descendants
                if ($status == Status::INACTIVE) {
                    $this->category->descendants()->update(['status' => $status]);
                }

                // Activating child category, activates all ancestors
                if ($status == Status::ACTIVE) {
                    $this->category->ancestors()->update(['status' => $status]);
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
            return $this->category->update(['position' => $position]);
        });
    }

    /**
     *
     * @param array $attributes
     * @return Category
     * @throws Throwable
     */
    public function update(array $attributes): Category
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->category->update([
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

            return $this->category;
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
            $this->category->makeRoot(0);

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
            /**
             * @var Category $parent
             */
            $parent = $this->category->findOrFail($parent_id);

            // In the case of changing the parent, we must prophylactically
            // change the status of the category (and descendants) to the same
            // as the parent to avoid the situation where the subcategory
            // is active and the parent is not.
            $this->category->update(['status' => $parent->status]);
            $this->category->descendants()->update(['status' => $parent->status]);

            $this->category->moveTo(0, $parent_id);

            return true;
        });
    }
}
