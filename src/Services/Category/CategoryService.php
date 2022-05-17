<?php

namespace N1ebieski\ICore\Services\Category;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\ValueObjects\Category\Status;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\DeleteInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Services\Interfaces\GlobalDeleteInterface;
use N1ebieski\ICore\Services\Interfaces\StatusUpdateInterface;
use N1ebieski\ICore\Services\Interfaces\PositionUpdateInterface;

/**
 *
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class CategoryService implements
    CreateInterface,
    UpdateInterface,
    StatusUpdateInterface,
    PositionUpdateInterface,
    DeleteInterface,
    GlobalDeleteInterface
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
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Config $config
     * @param Collect $collect
     * @param DB $db
     */
    public function __construct(
        Category $category,
        Config $config,
        Collect $collect,
        DB $db
    ) {
        $this->category = $category;

        $this->collect = $collect;
        $this->db = $db;
        $this->config = $config;
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
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->category->name = $attributes['name'];
            $this->category->icon = $attributes['icon'] ?? null;

            if ($attributes['parent_id'] !== null) {
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
                $parent_id = $this->category->find($attributes['parent_id']);
            }

            if (isset($attributes['clear'])) {
                if ((bool)$attributes['clear'] === true) {
                    $this->category->newQuery()->poliType()->delete();
                    $parent_id = null;
                }
            }

            return $this->category->createFromArray(
                json_decode($attributes['names'], true),
                $parent_id ?? null
            );
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
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
                if ($c = $this->category->find($id)) {
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
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
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
        });
    }

    /**
     * [moveToRoot description]
     * @return void [description]
     */
    public function moveToRoot(): void
    {
        $this->db->transaction(function () {
            $this->category->makeRoot(0);
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
            if ($parent = $this->category->findOrFail($parent_id)) {
                // In the case of changing the parent, we must prophylactically
                // change the status of the category (and descendants) to the same
                // as the parent to avoid the situation where the subcategory
                // is active and the parent is not.
                $this->category->update(['status' => $parent->status]);
                $this->category->descendants()->update(['status' => $parent->status]);

                $this->category->moveTo(0, $parent_id);
            }
        });
    }
}
