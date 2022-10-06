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

namespace N1ebieski\ICore\Models\Traits;

use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\Builder;

/**
 * Franzose/ClosureTable does not have include polymorphic models.
 * That trait adds that feature.
 */
trait HasFixForPolymorphicClosureTable
{
    /**
     * Cached "previous" (i.e. before the model is moved) direct ancestor id of this model.
     *
     * @var int
     */
    private $previousParentId;

    /**
     * Cached "previous" (i.e. before the model is moved) model position.
     *
     * @var int
     */
    private $previousPosition;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        static::bootTraits();

        static::saving(static function (Entity $entity) {
            if ($entity->isDirty($entity->getPositionColumn())) {
                $latest = static::getLatestPosition($entity);

                if (!$entity->isMoved) {
                    $latest--;
                }

                $entity->position = max(0, min($entity->position, $latest));
            } elseif (!$entity->exists) {
                $entity->position = static::getLatestPosition($entity);
            }
        });

        // When entity is created, the appropriate
        // data will be put into the closure table.
        static::created(static function (Entity $entity) {
            $entity->previousParentId = null;
            $entity->previousPosition = null;

            $descendant = $entity->getKey();
            $ancestor = $entity->parent_id ?? $descendant;

            $entity->closure->insertNode($ancestor, $descendant);
        });

        static::saved(static function (Entity $entity) {
            $parentIdChanged = $entity->isDirty($entity->getParentIdColumn());

            if ($parentIdChanged || $entity->isDirty($entity->getPositionColumn())) {
                $entity->reorderSiblings();
            }

            if ($entity->closure->ancestor === null) {
                $primaryKey = $entity->getKey();
                $entity->closure->ancestor = $primaryKey;
                $entity->closure->descendant = $primaryKey;
                $entity->closure->depth = 0;
            }

            if ($parentIdChanged) {
                $entity->closure->moveNodeTo($entity->parent_id);
            }
        });
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @param array|null $connection
     * @return void
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $instance = parent::newFromBuilder($attributes);
        $instance->previousParentId = $instance->parent_id;
        $instance->previousPosition = $instance->position;
        return $instance;
    }

    /**
     * Sets new parent id and caches the old one.
     *
     * @param int $value
     */
    public function setParentIdAttribute($value)
    {
        if ($this->parent_id === $value) {
            return;
        }

        $parentId = $this->getParentIdColumn();
        $this->previousParentId = $this->original[$parentId] ?? null;
        $this->attributes[$parentId] = $value;
    }

    /**
     * Sets new position and caches the old one.
     *
     * @param int $value
     */
    public function setPositionAttribute($value)
    {
        if ($this->position === $value) {
            return;
        }

        $position = $this->getPositionColumn();
        $this->previousPosition = $this->original[$position] ?? null;
        $this->attributes[$position] = max(0, (int) $value);
    }

    /**
     * Returns sibling query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeSibling(Builder $builder)
    {
        return parent::scopeSibling($builder)
            ->when($this->model_id !== null, function (Builder $query) {
                return $query->poli();
            }, function (Builder $query) {
                return $query->poliType();
            });
    }

    /**
     * Model jest polimorficzny i sprawdzanie rodzeństwa musi się odbywać z użyciem
     * $this->model_type
     *
     * Reorders node's siblings when it is moved to another position or ancestor.
     *
     * @return void
     */
    private function reorderSiblings()
    {
        $position = $this->getPositionColumn();

        if ($this->previousPosition !== null) {
            $this
                ->where($this->getKeyName(), '<>', $this->getKey())
                ->where($this->getParentIdColumn(), '=', $this->previousParentId)
                ->where($position, '>', $this->previousPosition)
                ->when($this->model_id !== null, function (Builder $query) {
                    return $query->poli();
                }, function (Builder $query) {
                    return $query->poliType();
                })
                ->decrement($position);
        }

        $this
            ->sibling()
            ->where($this->getKeyName(), '<>', $this->getKey())
            ->where($position, '>=', $this->position)
            ->increment($position);
    }

    /**
     * Gets the next sibling position after the last one.
     *
     * @param Entity $entity
     *
     * @return int
     */
    public static function getLatestPosition(Entity $entity)
    {
        $positionColumn = $entity->getPositionColumn();
        $parentIdColumn = $entity->getParentIdColumn();

        $latest = $entity->select($positionColumn)
            ->where($parentIdColumn, '=', $entity->parent_id)
            ->latest($positionColumn)
            ->when($entity->model_id !== null, function (Builder $query) {
                return $query->poli();
            }, function (Builder $query) {
                return $query->poliType();
            })
            ->first();

        $position = $latest !== null ? $latest->position : -1;

        return $position + 1;
    }
}
