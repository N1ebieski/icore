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
use Franzose\ClosureTable\Extensions\Collection;
use Franzose\ClosureTable\Contracts\EntityInterface;

/**
 * Franzose/ClosureTable does not have support for Multi Lang feature.
 * That trait adds that feature.
 */
trait HasFixForMultiLangClosureTable
{
    /**
     * Saves models from the given attributes array.
     *
     * @param array $tree
     * @param EntityInterface $parent
     *
     * @return Collection
     * @throws Throwable
     */
    public static function createFromArray(array $tree, EntityInterface $parent = null)
    {
        $collection = [];

        $create = function (array $items, EntityInterface $parent = null) use (&$create, &$collection) {
            $entities = [];

            foreach ($items as $item) {
                $children = $item[static::CHILDREN_RELATION_NAME] ?? [];

                /**
                 * @var Entity $entity
                 */
                $entity = new static($item);
                $entity->parent_id = $parent ? $parent->getKey() : null;
                $entity->save();

                $entity->currentLang->makeService()->create([
                    'name' => $item['name'],
                    $this->getBaseName() => $entity
                ]);

                if ($children !== null) {
                    $entity->addChildren($create($children, $entity)->all());
                }

                $entities[] = $collection[] = $entity;
            }

            return new Collection($entities);
        };

        $create($tree, $parent);

        return new Collection($collection);
    }

    /**
     *
     * @return string
     */
    protected function getBaseName(): string
    {
        return lcfirst(class_basename($this::class));
    }
}
